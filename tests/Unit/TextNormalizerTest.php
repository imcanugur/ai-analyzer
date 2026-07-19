<?php

namespace Tests\Unit;

use App\Services\Extractors\TextNormalizer;
use Tests\TestCase;

class TextNormalizerTest extends TestCase
{
    public function test_normalizer_strips_zero_width_spaces_and_unwraps_lines(): void
    {
        $raw = "\u{200B}AI Destekli Geliştirme Ortamlarında (AI-Assisted IDE)\u{200B}\n\u{200B}Özet\u{200B}\u{200B}Bu\u{200B}\u{200B}çalışma,\u{200Byapay\u{200B}\u{200B}zeka\u{200B}\u{200B}destekli\u{200B}\n\u{200B}platformlarının\u{200B}\u{200B}(AI-assisted\u200B\u200bIDEs)\u{200B}\u{200bdil\u200B\u200bbazlı\u200B\u200bmobil\u200B\n\u{200buygulama\u200B\u200bgeliştirme\u200B\u200bsüreçlerindeki\u200B\u200bkalitesini\u200B\u200bve\u200B\n\u200bbaşarım\u200b\u200bölçütlerini\u200b\u200bincelemiştir.";

        $normalizer = new TextNormalizer;
        $normalized = $normalizer->normalize($raw);

        $this->assertStringNotContainsString("\u{200B}", $normalized);
        $this->assertStringContainsString('AI Destekli Geliştirme Ortamlarında (AI-Assisted IDE)', $normalized);
        $this->assertStringContainsString('Özet Bu çalışma, yapay zeka destekli platformlarının (AI-assisted IDEs) dil bazlı mobil uygulama geliştirme süreçlerindeki kalitesini ve başarım ölçütlerini incelemiştir.', $normalized);
    }
}
