<?php

namespace App\Services;

use App\Contracts\PathGenerator;
use App\Models\Analysis;
use App\Models\Media;
use App\Models\StageRoute;

class ReportService
{
    public function __construct(
        protected readonly PathGenerator $pathGenerator,
        protected readonly MediaService $mediaService
    ) {}

    /**
     * Compile analysis results and generate both JSON and PDF reports sorted strictly by stage_routes sort_order.
     *
     * @return array{json: Media, pdf: Media}
     */
    public function generateReports(Analysis $analysis): array
    {
        // 1. Fetch stage routes sort order and active names
        $stageOrders = StageRoute::active()->pluck('sort_order', 'stage')->toArray();

        // 2. Fetch analysis results sorted by stage_routes sort_order
        $results = $analysis->results()
            ->get()
            ->sortBy(function ($res) use ($stageOrders) {
                $sKey = is_object($res->stage) ? $res->stage->value : (string) $res->stage;
                return $stageOrders[$sKey] ?? 999;
            });

        $compiledData = [
            'analysis_id' => $analysis->id,
            'submission_id' => $analysis->submission_id,
            'type' => $analysis->type,
            'model' => $analysis->model,
            'generated_at' => now()->toIso8601String(),
            'stages' => [],
        ];

        foreach ($results as $result) {
            $text = $result->payload['text'] ?? '';

            // Decode JSON strings so they appear as structured arrays/objects in the JSON report
            if (is_string($text) && (str_starts_with(trim($text), '{') || str_starts_with(trim($text), '['))) {
                $decoded = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $text = $decoded;
                }
            }

            $stageKey = is_object($result->stage) ? $result->stage->value : (string) $result->stage;
            $statusKey = is_object($result->status) ? $result->status->value : (string) $result->status;

            $compiledData['stages'][$stageKey] = [
                'status' => $statusKey,
                'text' => $text,
                'tokens' => $result->tokens,
                'execution_time' => $result->execution_time,
            ];
        }

        $htmlContent = $this->compileHtmlReport($analysis, $compiledData);

        $pdfMedia = $this->mediaService->saveRawContent(
            contents: $htmlContent,
            filename: "{$analysis->id}.pdf",
            mime: 'application/pdf',
            mediable: $analysis,
            directory: 'reports',
            forcePath: "{$analysis->id}.pdf"
        );

        // Generate JSON content
        $jsonContent = json_encode($compiledData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Save JSON as media directly bound to Analysis model
        $jsonMedia = $this->mediaService->saveRawContent(
            contents: $jsonContent,
            filename: "{$analysis->id}.json",
            mime: 'application/json',
            mediable: $analysis,
            directory: 'reports',
            forcePath: "{$analysis->id}.json"
        );

        return [
            'json' => $jsonMedia,
            'pdf' => $pdfMedia,
        ];
    }

    /**
     * Build clean readable print layout content ordered strictly by stage_routes sort_order.
     */
    protected function compileHtmlReport(Analysis $analysis, array $data): string
    {
        $stageRoutes = StageRoute::active()->ordered()->get()->keyBy('stage');
        $stagesHtml = '';

        foreach ($data['stages'] as $stageKey => $stageData) {
            if ($stageKey === 'extract' || $stageKey === 'report') {
                continue;
            }

            $stageRoute = $stageRoutes->get($stageKey);
            $displayName = $stageRoute->name ?? strtoupper($stageKey);

            $stagesHtml .= '<div style="margin-bottom: 24px;">';
            $stagesHtml .= '<h2 style="color: #1e3a8a; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 12px;">'.e($displayName).'</h2>';
            
            $text = $stageData['text'] ?? '';
            if (is_array($text) || is_object($text)) {
                $text = json_encode($text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            
            $stagesHtml .= '<div style="font-size: 13px; color: #334155; white-space: pre-wrap; line-height: 1.6;">'.nl2br(e((string) $text)).'</div>';
            $stagesHtml .= '<div style="margin-top: 8px; font-size: 11px; color: #64748b;">Tokens: '.($stageData['tokens'] ?? 0).' | Execution Time: '.($stageData['execution_time'] ?? 0).'ms</div>';
            $stagesHtml .= '</div>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Analysis Report - {$analysis->id}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 40px; color: #1e293b; line-height: 1.6; }
        h1 { color: #0f172a; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 16px; }
        .metadata { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; font-size: 13px; }
        .metadata p { margin: 4px 0; }
        hr { border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>AI Manuscript Analysis Report</h1>
    <div class="metadata">
        <p><strong>Analysis Run ID:</strong> {$analysis->id}</p>
        <p><strong>Model Assigned:</strong> {$analysis->model}</p>
        <p><strong>Report Generated At:</strong> {$data['generated_at']}</p>
    </div>
    {$stagesHtml}
</body>
</html>
HTML;
    }
}
