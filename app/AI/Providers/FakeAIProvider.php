<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIResponse;
use Illuminate\Support\Facades\Log;

class FakeAIProvider implements AIProviderInterface
{
    public function generate(string $prompt, array $options = [], ?string $systemPrompt = null): AIResponse
    {
        $stage = $options['stage'] ?? 'general';
        $delay = (int) config('demo.fake_ai_delay_seconds', 3);

        Log::info("[FakeAIProvider] Demo Mode AI Simulation running for stage [{$stage}] (delay: {$delay}s)");

        if ($delay > 0) {
            sleep($delay);
        }

        $mockOutput = $this->getMockOutputForStage($stage);

        return new AIResponse(
            text: $mockOutput,
            tokens: rand(450, 1200),
            executionTime: $delay * 1000,
            rawResponse: ['status' => 'success', 'demo_mode' => true],
            metadata: [
                'driver' => 'fake_demo_ai',
                'model' => ($options['model'] ?? 'gemma2').'-demo-simulated',
            ]
        );
    }

    private function getMockOutputForStage(string $stage): string
    {
        return match ($stage) {
            'summary' => json_encode([
                'title' => 'Yapay Zeka Destekli Kod Analiz Özeti',
                'summary' => 'Bu doküman AI destekli geliştirme ortamlarını (AI-Assisted IDE) ve yazılım mimarisi süreçlerini incelemektedir. Modellerin kod üretimi, hata tespiti ve performans optimizasyonlarına olan etkisi kapsamlı biçimde ele alınmıştır.',
                'key_takeaways' => [
                    'AI platform mimarileri Clean Architecture ve SOLID prensiplerini benimsemelidir.',
                    'Statik analiz ve LLM çıktılarının otomatik onarımı yüksek güvenilirlik sağlar.',
                    'İzole demo ortamları maliyet optimizasyonu ve güvenli kullanıcı deneyimi sunar.'
                ],
                'academic_score' => 94.5
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'grammar' => json_encode([
                'overall_score' => 96.0,
                'corrections' => [
                    ['original' => 'kodlar başarılı yazıldı', 'suggestion' => 'Kodlar başarıyla yazıldı.', 'reason' => 'Zarf kullanımı düzeltildi.'],
                    ['original' => 'depency injection', 'suggestion' => 'Dependency Injection', 'reason' => 'Yazım terimi düzeltildi.']
                ],
                'style_notes' => 'Akademik ve teknik üslup oldukça tutarlı, anlatım nettir.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'references' => json_encode([
                'found_references' => 12,
                'verified_citations' => 11,
                'missing_dois' => 1,
                'citation_format' => 'APA 7th Edition',
                'accuracy_score' => 91.0
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'similarity' => json_encode([
                'similarity_percentage' => 4.2,
                'originality_score' => 95.8,
                'matched_sources' => [],
                'verdict' => 'Doküman yüksek derecede özgündür.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'reviewer' => json_encode([
                'overall_verdict' => 'Kabul Edilebilir (Minor Revision)',
                'strengths' => ['Güçlü teorik altyapı', 'Modüler mimari tasarımı'],
                'weaknesses' => ['Daha fazla deneysel veri eklenebilir'],
                'recommendation_score' => 88.5
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            default => json_encode([
                'stage' => $stage,
                'status' => 'completed',
                'analysis' => "Demo modunda [{$stage}] aşaması için simüle edilmiş AI analiz sonucu üretilmiştir.",
                'score' => 90.0,
                'confidence' => 0.98
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        };
    }
}
