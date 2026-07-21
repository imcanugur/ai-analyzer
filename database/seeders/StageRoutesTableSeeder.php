<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StageRoutesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('stage_routes')->delete();
        
        \DB::table('stage_routes')->insert(array (
            0 => 
            array (
                'id' => '019f576f-e42c-71c2-a6fa-c4da0a428df2',
                'stage' => 'similarity',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:54:12',
                'name' => 'Benzerlik ve Özgünlük Analizi',
                'description' => 'Metindeki terim ve içerik örtüşmelerini tespit eder, özgünlük değerlendirmesi sunar.',
                'prompt_template' => '# İNCELENEN METİN
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 40,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => '0.10',
                'max_tokens' => 2048,
                'output_format' => 'json',
                'config' => NULL,
            ),
            1 => 
            array (
                'id' => '019f576f-e433-7336-b5cc-542a93676848',
                'stage' => 'plagiarism',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:57:58',
                'name' => 'İntihal ve Etik Denetimi',
                'description' => 'Metni intihal riski, kopyala-yapıştır kalıpları ve doğrudan alıntı hataları açısından denetler.',
                'prompt_template' => '# METİN
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 60,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => '0.10',
                'max_tokens' => 2048,
                'output_format' => 'json',
                'config' => NULL,
            ),
            2 => 
            array (
                'id' => '019f576f-e430-73da-828c-68cc59e2aee2',
                'stage' => 'reviewer',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:57:58',
                'name' => 'Akademik Hakem Değerlendirmesi',
                'description' => 'Makaleyi özgünlük, yöntem, bulgular ve bilimsel katkı açılarından 10 kriter üzerinden hakem gözüyle değerlendirir.',
                'prompt_template' => '# MAKALE
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 50,
                'is_active' => true,
                'dependencies' => '["summary","grammar"]',
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 300,
                'temperature' => '0.40',
                'max_tokens' => 4096,
                'output_format' => 'json',
                'config' => NULL,
            ),
            3 => 
            array (
                'id' => '019f576f-e429-73a3-9f92-341268220048',
                'stage' => 'references',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:57:58',
                'name' => 'Kaynakça ve Atıf Doğrulaması',
            'description' => 'Kaynakça listenizi atıf standartları (APA, IEEE, MLA), DOI eksikleri ve format hataları açısından denetler.',
                'prompt_template' => '# KAYNAKÇA
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 30,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => '0.10',
                'max_tokens' => 2048,
                'output_format' => 'json',
                'config' => NULL,
            ),
            4 => 
            array (
                'id' => '019f576f-e436-7254-bb93-3cff15ac6b1e',
                'stage' => 'readability',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:57:58',
                'name' => 'Okunabilirlik ve Anlaşılırlık Analizi',
                'description' => 'Metnin okunabilirlik skorunu, cümle karmaşıklığını ve akademik akıcılığını ölçer.',
                'prompt_template' => '# METİN
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 70,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 120,
                'temperature' => '0.10',
                'max_tokens' => 2048,
                'output_format' => 'json',
                'config' => NULL,
            ),
            5 => 
            array (
                'id' => '019f7b83-a3e5-73fa-af75-8c8b2c67933f',
                'stage' => 'extract',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-19 17:54:12',
                'updated_at' => '2026-07-19 18:42:02',
                'name' => 'Dosya ve Metin Çıkarımı',
                'description' => 'Yüklenen PDF, Word ve metin dosyalarını okur, ham metni ayıklar.',
                'prompt_template' => NULL,
                'system_prompt' => NULL,
                'sort_order' => 0,
                'is_active' => true,
                'dependencies' => '[]',
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 60,
                'temperature' => NULL,
                'max_tokens' => NULL,
                'output_format' => 'text',
                'config' => NULL,
            ),
            6 => 
            array (
                'id' => '019f576f-e426-7277-b55b-ff2723bd44d0',
                'stage' => 'grammar',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-19 17:57:58',
                'name' => 'Dilbilgisi ve Stil Analizi',
                'description' => 'Akademik Türkçe yazım kuralları, TDK uyumu, anlatım bozuklukları ve terminoloji tutarlılığını detaylıca inceler.',
                'prompt_template' => '# METİN
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
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 20,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'skip',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => '0.10',
                'max_tokens' => 4096,
                'output_format' => 'json',
                'config' => NULL,
            ),
            7 => 
            array (
                'id' => '019f7b83-a411-736e-89ab-b6fc745d8061',
                'stage' => 'report',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-19 17:54:12',
                'updated_at' => '2026-07-19 18:00:21',
                'name' => 'Final Rapor ve PDF Oluşturma',
                'description' => 'Tüm analiz çıktısını derler, indirilebilir PDF ve JSON raporlarını üretir.',
                'prompt_template' => NULL,
                'system_prompt' => NULL,
                'sort_order' => 80,
                'is_active' => true,
                'dependencies' => '[]',
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 60,
                'temperature' => NULL,
                'max_tokens' => NULL,
                'output_format' => 'pdf',
                'config' => NULL,
            ),
            8 => 
            array (
                'id' => '019f576f-e422-7243-b6c3-567533ab34c7',
                'stage' => 'summary',
                'model' => 'qwen2.5:3b',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'created_at' => '2026-07-12 17:46:18',
                'updated_at' => '2026-07-21 11:24:09',
                'name' => 'Akademik Özet Çıkarımı',
                'description' => 'Makale metnini anlam bütünlüğünü koruyarak başlık, amaç, yöntem, bulgular ve sonuç açısından yapısal olarak özetler.',
                'prompt_template' => '# METİN
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
- yazarlar: Araştıracının veya arastımacıalrın bilgileri

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
    "anahtar_kelimeler": [],
		"yazarlar": []
}',
                'system_prompt' => '# SİSTEM

Sen AI Analyzer platformunun akademik analiz motorusun.

## Kurallar
- Daima Türkçe cevap ver.
- Yalnızca geçerli JSON döndür. Markdown kod bloğu (```json) kullanma.
- Bilgi uydurma. Metinde olmayan şeyleri yazma.
- Eksik veya analiz edilemeyen alanlar için null kullan.',
                'sort_order' => 10,
                'is_active' => true,
                'dependencies' => '["extract"]',
                'on_failure' => 'fail_pipeline',
                'max_retries' => 3,
                'timeout_seconds' => 180,
                'temperature' => '0.30',
                'max_tokens' => 2048,
                'output_format' => 'json',
                'config' => NULL,
            ),
        ));
        
        
    }
}