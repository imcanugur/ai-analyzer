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
     * Compile analysis results and generate both JSON and PDF reports.
     *
     * @return array{json: Media, pdf: Media}
     */
    public function generateReports(Analysis $analysis): array
    {
        // 1. Fetch analysis results
        $results = $analysis->results()->get();

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
        $jsonContent = json_encode($compiledData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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
     * Build clean readable print layout content.
     */
    protected function compileHtmlReport(Analysis $analysis, array $data): string
    {
        $stagesHtml = '';
        foreach ($data['stages'] as $stageKey => $stageData) {
            if ($stageKey === 'extract') {
                continue;
            }

            $stageRoute = StageRoute::where('stage', $stageKey)->first();
            $displayName = $stageRoute->name ?? ucfirst($stageKey);

            $stagesHtml .= '<h2>'.e($displayName).'</h2>';
            $text = $stageData['text'] ?? '';
            if (is_array($text) || is_object($text)) {
                $text = json_encode($text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $stagesHtml .= '<p>'.nl2br(e($text)).'</p>';
            $stagesHtml .= "<small>Tokens: {$stageData['tokens']} | Time: {$stageData['execution_time']}ms</small><hr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Analysis Report - {$analysis->id}</title>
    <style>
        body { font-family: sans-serif; margin: 40px; color: #333; line-height: 1.6; }
        h1 { color: #1a365d; border-bottom: 2px solid #2b6cb0; padding-bottom: 10px; }
        h2 { color: #2b6cb0; margin-top: 30px; }
        hr { border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0; }
        small { color: #718096; }
    </style>
</head>
<body>
    <h1>AI Analysis Report</h1>
    <p><strong>Analysis ID:</strong> {$analysis->id}</p>
    <p><strong>Model:</strong> {$analysis->model}</p>
    <p><strong>Generated At:</strong> {$data['generated_at']}</p>
    <hr>
    {$stagesHtml}
</body>
</html>
HTML;
    }
}
