<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contracts\NodeRepositoryInterface;
use App\Contracts\StageRouteRepositoryInterface;
use Illuminate\Database\Seeder;

class StageRouteSeeder extends Seeder
{
    public function __construct(
        protected StageRouteRepositoryInterface $routeRepository,
        protected NodeRepositoryInterface $nodeRepository
    ) {}

    /**
     * Seed 100% of pipeline stages into database table, including extraction and report generation.
     */
    public function run(): void
    {
        $nodes = $this->nodeRepository->all();

        $node = $nodes->first(fn ($n) => str_contains($n->endpoint, '11434'));

        $systemPrompt = <<<'PROMPT'
# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.
PROMPT;

        $routes = [
            'extract' => [
                'name' => 'Dosya ve Metin Çıkarımı',
                'description' => 'Yüklenen PDF, Word ve metin dosyalarını okur, ham metni ayıklar.',
                'dependencies' => [],
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 60,
                'temperature' => null,
                'max_tokens' => null,
                'output_format' => 'text',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 0,
                'is_active' => true,
                'system_prompt' => null,
                'prompt_template' => null,
            ],

            'summary' => [
                'name' => 'Akademik Özet Çıkarımı',
                'description' => 'Makale metnini anlam bütünlüğünü koruyarak başlık, amaç, yöntem, bulgular ve sonuç açısından yapısal olarak özetler.',
                'dependencies' => ['extract'],
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => 0.30,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 10,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# METİN
{{ text }}

---

# ROL VE GÖREV
Sen deneyimli bir akademik araştırmacı ve bilimsel editörsün. Görevin yukarıdaki metni anlam bütünlüğünü koruyarak yapısal olarak özetlemektir.

Aşağıdaki alanları metne göre doldur:
- baslik: Araştırmanın konusunu yansıtan kısa başlık.
- amac: Araştırmanın amacı ve hedefi.
- problem: Ele alınan temel araştırma sorusu veya problem.
- yontem: Kullanılan araştırma yöntemi, veri toplama ve analiz teknikleri.
- bulgular: Elde edilen sayısal/nitel temel sonuçlar.
- tartisma: Bulguların literatürdeki yeri veya sınırlılıkları.
- sonuc: Araştırmanın ana çıkarımları ve gelecek önerileri.
- katkilar: Çalışmanın bilimsel alana sağladığı özgün katkı.
- anahtar_kelimeler: Metinle ilgili 3-6 adet anahtar kelime.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "baslik": "",
    "amac": "",
    "problem": "",
    "yontem": "",
    "bulgular": "",
    "tartisma": "",
    "sonuc": "",
    "katkilar": "",
    "anahtar_kelimeler": []
}
PROMPT,
            ],

            'grammar' => [
                'name' => 'Dilbilgisi ve Stil Analizi',
                'description' => 'Akademik Türkçe yazım kuralları, TDK uyumu, anlatım bozuklukları ve terminoloji tutarlılığını detaylıca inceler.',
                'dependencies' => ['extract'],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => 0.10,
                'max_tokens' => 4096,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 20,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# METİN
{{ text }}

---

# ROL VE GÖREV
Sen akademik Türkçe yazım kuralları ve üslubu konusunda uzman bir editörsün.
Yukarıdaki metni şu kurallara göre incele:
- Yazım kuralları (TDK uyumu, büyük/küçük harf, bitişik/ayrı yazım)
- Noktalama işaretleri (virgül, noktalı virgül vb. doğru kullanımı)
- Dil bilgisi (özne-yüklem uyumu, ek yanlışlıkları, çatı uyumu)
- Anlatım bozuklukları (gereksiz sözcük, çelişen sözcüklerin bir arada kullanımı)
- Akademik üslup (günlük konuşma dili yerine resmi ve edilgen anlatım)

# ÇIKTI FORMATI
Metni 0 ile 100 arasında değerlendir.
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "puan": 0,
    "genel_degerlendirme": "Metnin genel dil kalitesi hakkında özet değerlendirme.",
    "hatalar": [
        {
            "onem": "Dusuk | Orta | Yuksek",
            "kategori": "Yazim | Noktalama | Dilbilgisi | Anlatim | Uslup | Terminoloji",
            "orijinal": "Metinden hata içeren orijinal cümle",
            "oneri": "Düzeltilmiş önerilen ifade",
            "aciklama": "Hatanın kısa açıklaması"
        }
    ]
}
PROMPT,
            ],

            'references' => [
                'name' => 'Kaynakça ve Atıf Doğrulaması',
                'description' => 'Kaynakça listenizi atıf standartları (APA, IEEE, MLA), DOI eksikleri ve format hataları açısından denetler.',
                'dependencies' => ['extract'],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => 0.10,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 30,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# KAYNAKÇA
{{ text }}

---

# ROL VE GÖREV
Sen akademik kaynakça ve atıf sistemleri konusunda uzman bir editörsün. Görevin yukarıdaki kaynakçayı analiz etmektir.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "kaynak_sayisi": 0,
    "yinelenen_sayisi": 0,
    "doi_eksikleri": ["DOI eksik olan kaynaklar"],
    "url_eksikleri": ["URL eksik olan kaynaklar"],
    "eksik_bilgiler": ["Eksik bilgili kaynaklar"],
    "bicim_hatalari": ["Format hatası olan kaynaklar"],
    "eski_kaynaklar": ["Eski kaynaklar"],
    "oneriler": ["Düzenleme önerileri"]
}
PROMPT,
            ],

            'similarity' => [
                'name' => 'Benzerlik ve Özgünlük Analizi',
                'description' => 'Metindeki terim ve içerik örtüşmelerini tespit eder, özgünlük değerlendirmesi sunar.',
                'dependencies' => ['extract'],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => 0.10,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 40,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# İNCELENEN METİN
{{ text }}

---

# ROL VE GÖREV
Sen akademik benzerlik ve araştırma etiği konusunda uzman bir inceleme editörüsün. Görevin metni özgünlük açısından değerlendirmektir.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "genel_risk": "Dusuk | Orta | Yuksek",
    "genel_degerlendirme": "Genel benzerlik oranı ve özgünlük durumu hakkında değerlendirme.",
    "eslesmeler": [
        {
            "kaynak": "Eşleşen kaynak",
            "benzerlik": 0.0,
            "risk": "Dusuk | Orta | Yuksek",
            "yorum": "Eşleşme yorumu",
            "oneri": "Özgünleştirme önerisi"
        }
    ]
}
PROMPT,
            ],

            'reviewer' => [
                'name' => 'Akademik Hakem Değerlendirmesi',
                'description' => 'Makaleyi özgünlük, yöntem, bulgular ve bilimsel katkı açılarından 10 kriter üzerinden hakem gözüyle değerlendirir.',
                'dependencies' => ['summary', 'grammar'],
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 300,
                'temperature' => 0.40,
                'max_tokens' => 4096,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 50,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# MAKALE
{{ text }}

---

# ROL VE GÖREV
Sen uluslararası akademik dergilerde görev yapan kıdemli bir hakemsin. Görevin yukarıdaki makaleyi tarafsız, yapıcı ve bilimsel kriterlere göre değerlendirmektir.

Aşağıdaki 10 kriteri 1.0 ile 10.0 arasında puanla:
1. baslik, 2. ozgunluk, 3. literatur, 4. yontem, 5. bulgular, 6. tartisma, 7. sonuc, 8. kaynakca, 9. bilimsel_katki, 10. yazim

CRITICAL INSTRUCTION:
- DİKKAT: JSON şablonu içindeki örnek string açıklamalarını AYNEN KOPYALAMA! "guclu_yonler", "zayif_yonler", "oneriler" alanlarına metinden bizzat çıkardığın GERÇEK VE ÖZGÜN analiz cümlelerini yaz.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "genel_puan": 7.5,
    "karar": "Kucuk Revizyon",
    "ozet": "İncelenen makalenin genel değerlendirmesi.",
    "guclu_yonler": ["Güçlü yön 1", "Güçlü yön 2"],
    "zayif_yonler": ["Zayıf yön 1", "Zayıf yön 2"],
    "oneriler": ["Öneri 1", "Öneri 2", "Öneri 3"],
    "puanlar": {
        "baslik": 7.0, "ozgunluk": 8.0, "literatur": 7.5, "yontem": 8.0, "bulgular": 7.0,
        "tartisma": 7.5, "sonuc": 7.0, "kaynakca": 8.0, "bilimsel_katki": 7.5, "yazim": 8.0
    }
}
PROMPT,
            ],

            'plagiarism' => [
                'name' => 'İntihal ve Etik Denetimi',
                'description' => 'Metni intihal riski, kopyala-yapıştır kalıpları ve doğrudan alıntı hataları açısından denetler.',
                'dependencies' => ['extract'],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => 0.10,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 60,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# METİN
{{ text }}

---

# ROL VE GÖREV
Sen akademik intihal ve etik ihlalleri konusunda uzman bir denetçisisin. Görevin metni intihal riski açısından incelemektir.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "intihal_skoru": 0,
    "risk_seviyesi": "Dusuk | Orta | Yuksek",
    "genel_degerlendirme": "Metnin özgünlük değerlendirmesi.",
    "riskli_bolumler": [
        {
            "metin_kesiti": "Riskli ifade",
            "risk_nedeni": "Neden",
            "oneri": "Öneri"
        }
    ]
}
PROMPT,
            ],

            'readability' => [
                'name' => 'Okunabilirlik ve Anlaşılırlık Analizi',
                'description' => 'Metnin okunabilirlik skorunu, cümle karmaşıklığını ve akademik akıcılığını ölçer.',
                'dependencies' => ['extract'],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => 0.10,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 70,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# METİN
{{ text }}

---

# ROL VE GÖREV
Sen dilbilim ve metin okunabilirliği konusunda uzman bir analistsin. Görevin metnin okunabilirlik seviyesini değerlendirmektir.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. SADECE aşağıdaki yapıyı birebir kullan:

{
    "okunabilirlik_skoru": 0,
    "seviye": "Kolay | Orta | Zor | Akademik/İleri",
    "genel_degerlendirme": "Metnin anlaşılırlığı hakkında değerlendirme.",
    "iyilestirme_onerileri": ["Öneri 1"]
}
PROMPT,
            ],

            'report' => [
                'name' => 'Final Rapor ve PDF Oluşturma',
                'description' => 'Tüm analiz çıktısını derler, indirilebilir PDF ve JSON raporlarını üretir.',
                'dependencies' => [],
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 60,
                'temperature' => null,
                'max_tokens' => null,
                'output_format' => 'pdf',
                'model' => 'qwen2.5:3b',
                'node_id' => $node?->id,
                'sort_order' => 80,
                'is_active' => true,
                'system_prompt' => null,
                'prompt_template' => null,
            ],
        ];

        foreach ($routes as $stage => $data) {
            $this->routeRepository->updateOrCreate($stage, $data);
        }
    }
}
