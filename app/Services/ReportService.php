<?php

namespace App\Services;

use App\Contracts\PathGenerator;
use App\Contracts\ReportRepositoryInterface;
use App\Models\Analysis;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function __construct(
        protected readonly ReportRepositoryInterface $reportRepository,
        protected readonly PathGenerator $pathGenerator
    ) {}

    /**
     * Compile analysis results and generate both JSON and PDF reports.
     *
     * @param Analysis $analysis
     * @return array{json: Report, pdf: Report}
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
            $compiledData['stages'][$result->stage->value] = [
                'status' => $result->status->value,
                'text' => $result->payload['text'] ?? '',
                'tokens' => $result->tokens,
                'execution_time' => $result->execution_time,
            ];
        }

        // 2. Generate JSON Report
        $jsonReport = $this->reportRepository->create([
            'analysis_id' => $analysis->id,
            'type' => 'json',
            'metadata' => [
                'data' => $compiledData,
            ],
        ]);

        // 3. Generate PDF Report
        // Compile a clean HTML/printable string representing the report layout
        $htmlContent = $this->compileHtmlReport($analysis, $compiledData);
        
        // Write the PDF file to default storage disk
        $fileName = $this->pathGenerator->getPath(null, "{$analysis->id}.pdf", 'reports');
        Storage::disk(config('filesystems.default'))->put($fileName, $htmlContent);

        $pdfReport = $this->reportRepository->create([
            'analysis_id' => $analysis->id,
            'type' => 'pdf',
            'metadata' => [
                'path' => $fileName,
                'url' => Storage::disk(config('filesystems.default'))->url($fileName),
                'size' => strlen($htmlContent),
            ],
        ]);

        return [
            'json' => $jsonReport,
            'pdf' => $pdfReport,
        ];
    }

    /**
     * Build clean readable print layout content.
     */
    protected function compileHtmlReport(Analysis $analysis, array $data): string
    {
        $stagesHtml = '';
        foreach ($data['stages'] as $stageName => $stageData) {
            $stagesHtml .= "<h2>" . ucfirst($stageName) . "</h2>";
            $stagesHtml .= "<p>" . nl2br(e($stageData['text'])) . "</p>";
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
