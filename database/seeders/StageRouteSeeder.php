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
     * Seed default dynamic pipeline stages with real production prompt templates.
     */
    public function run(): void
    {
        $nodes = $this->nodeRepository->all();

        $node1 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11434'));
        $node2 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11435'));
        $node3 = $nodes->first(fn ($n) => str_contains($n->endpoint, '11436'));

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
            'summary' => [
                'name' => 'Akademik Özet Çıkarımı',
                'description' => 'Makale metnini anlam bütünlüğünü koruyarak başlık, amaç, yöntem, bulgular ve sonuç açısından yapısal olarak özetler.',
                'dependencies' => [],
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => 0.30,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5',
                'node_id' => $node1?->id,
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

KURALLAR:
- Bilgi ekleme, varsayım üretme.
- Metinde yer almayan alanlar için "Metinde bu bilgiye yer verilmemiştir." yaz.
- Akademik ve resmi dil kullan, teknik terimleri koru.

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. Asla başka bir metin yazma. Kendi JSON anahtarlarını üretme, SADECE aşağıdaki yapıyı birebir kullan:

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
                'dependencies' => [],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => 0.10,
                'max_tokens' => 4096,
                'output_format' => 'json',
                'model' => 'gemma2',
                'node_id' => $node2?->id,
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
- Terminoloji tutarlılığı (terimlerin tutarlı ve doğru kullanımı)
- Gereksiz tekrarlar ve okunabilirlik

# ÇIKTI FORMATI
Metni 0 ile 100 arasında değerlendir.
YALNIZCA geçerli JSON döndür. Asla başka bir metin yazma. Kendi JSON anahtarlarını üretme, SADECE aşağıdaki yapıyı birebir kullan:

{
    "puan": 0,
    "genel_degerlendirme": "Metnin genel dil kalitesi, akademik üslubu, güçlü ve zayıf yönleri hakkında 2-3 cümlelik özet değerlendirme.",
    "hatalar": [
        {
            "onem": "Dusuk | Orta | Yuksek",
            "kategori": "Yazim | Noktalama | Dilbilgisi | Anlatim | Uslup | Terminoloji",
            "orijinal": "Metinden hata içeren orijinal cümle veya ifade",
            "oneri": "Düzeltilmiş ve akademik üsluba uygun önerilen ifade",
            "aciklama": "Hatanın kısa ve net açıklaması"
        }
    ]
}
PROMPT,
            ],

            'references' => [
                'name' => 'Kaynakça ve Atıf Doğrulaması',
                'description' => 'Kaynakça listenizi atıf standartları (APA, IEEE, MLA), DOI eksikleri ve format hataları açısından denetler.',
                'dependencies' => [],
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => 0.10,
                'max_tokens' => 2048,
                'output_format' => 'json',
                'model' => 'qwen2.5',
                'node_id' => $node1?->id,
                'sort_order' => 30,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# KAYNAKÇA
{{ text }}

---

# ROL VE GÖREV
Sen akademik kaynakça ve atıf sistemleri (APA, IEEE, MLA, Chicago) konusunda uzman bir editörsün. Görevin yukarıdaki kaynakçayı analiz edip atıf standartlarına uyumunu ve eksikliklerini belirlemektir.

KONTROL EDİLECEKLER:
- APA, IEEE, MLA veya Chicago standardına uyum ve format tutarlılığı
- Eksik bilgiler: Yazar adı, yayın yılı, DOI numarası veya erişim URL'si
- Yinelenen (tekrar eden) kaynaklar
- Kaynakların güncelliği (eski kaynaklar) ve çeşitliliği

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. Asla başka bir metin yazma. Kendi JSON anahtarlarını üretme, SADECE aşağıdaki yapıyı birebir kullan (Sorun yoksa listeleri boş bırak):

{
    "kaynak_sayisi": 0,
    "yinelenen_sayisi": 0,
    "doi_eksikleri": ["DOI eksik olan kaynaklar"],
    "url_eksikleri": ["URL veya erişim bilgisi eksik olan online kaynaklar"],
    "eksik_bilgiler": ["Yazar, yıl, cilt/sayfa gibi bilgileri eksik olan kaynaklar"],
    "bicim_hatalari": ["Atıf standartlarına göre biçim/noktalama hatası olan kaynaklar"],
    "eski_kaynaklar": ["Tarihi referanslar hariç, güncelliğini yitirmiş eski kaynaklar"],
    "oneriler": ["Kaynakça düzenlemesi için somut öneriler"]
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
                'model' => 'gemma2',
                'node_id' => $node3?->id,
                'sort_order' => 40,
                'is_active' => true,
                'system_prompt' => $systemPrompt,
                'prompt_template' => <<<'PROMPT'
# MAKALE
{{ text }}

---

# ROL VE GÖREV
Sen uluslararası akademik dergilerde görev yapan kıdemli bir hakemsin. Görevin yukarıdaki makaleyi tarafsız, yapıcı ve bilimsel kriterlere göre değerlendirmektir.

Aşağıdaki 10 kriteri 1-10 arasında puanla:
1. baslik: Başlığın uygunluğu ve kapsamı
2. ozgunluk: Çalışmanın sunduğu yenilik ve özgünlük
3. literatur: Literatür taramasının güncelliği ve derinliği
4. yontem: Araştırma yönteminin doğruluğu ve amaca uygunluğu
5. bulgular: Bulguların sunumu ve verilerin analizi
6. tartisma: Bulguların literatürle karşılaştırılması
7. sonuc: Çıkarımların verilerle tutarlılığı
8. kaynakca: Kaynakların yeterliliği ve format uyumu
9. bilimsel_katki: Alanındaki teorik/pratik değer ve katkı
10. yazim: Akademik dil kalitesi ve akıcılık

KARAR SEÇENEKLERİ (Aşağıdakilerden birini seç):
- "Kabul"
- "Kucuk Revizyon"
- "Buyuk Revizyon"
- "Ret"

# ÇIKTI FORMATI
YALNIZCA geçerli JSON döndür. Asla başka bir metin yazma. Kendi JSON anahtarlarını üretme, SADECE aşağıdaki yapıyı birebir kullan:

{
    "genel_puan": 0.0,
    "karar": "",
    "ozet": "Makalenin genel değerlendirmesini içeren 3-4 cümlelik paragraf.",
    "guclu_yonler": ["Makalenin en az 2 güçlü yönü"],
    "zayif_yonler": ["Makalenin en az 2 zayıf yönü/eksikliği"],
    "oneriler": ["Yazara çalışmayı geliştirmesi için en az 3 somut öneri"],
    "puanlar": {
        "baslik": 0,
        "ozgunluk": 0,
        "literatur": 0,
        "yontem": 0,
        "bulgular": 0,
        "tartisma": 0,
        "sonuc": 0,
        "kaynakca": 0,
        "bilimsel_katki": 0,
        "yazim": 0
    }
}
PROMPT,
            ],
        ];

        foreach ($routes as $stage => $data) {
            $this->routeRepository->updateOrCreate($stage, $data);
        }
    }
}
