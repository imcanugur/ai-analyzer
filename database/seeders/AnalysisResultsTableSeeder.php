<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnalysisResultsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('analysis_results')->delete();
        
        \DB::table('analysis_results')->insert(array (
            0 => 
            array (
                'id' => '019f8494-b89d-71a9-a04a-04576a51641d',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'extract',
                'status' => 'completed',
                'score' => NULL,
            'payload' => '{"text":"AI Destekli Geliştirme Ortamlarında (AI-Assisted IDE) Dil ve Mobil Uygulama Başarımı Üzerine Karşılaştırmalı Bir Analiz Emirhan Sevimli Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye 230541138@firat.edu.tr Mehmet Fatih Şık Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye 220541021@firat.edu.tr Soner Eşki Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye 220542005@firat.edu.tr Burak Can Kahraman Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye 220541085@firat.edu.tr Uğur Can Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye 220542019@firat.edu.tr Muhammed Baykara Yazılım Mühendisliği Fırat Üniversitesi Elazığ, Türkiye mbaykara@gmail.com Özet Bu çalışma, yapay zeka destekli kodlama platformlarının (AI-assisted IDEs) dil bazlı mobil uygulama geliştirme süreçlerindeki kalitesini ve başarı ölçütlerini incelemiştir. Araştırma kapsamında Cursor ve Antigravity araçları kullanılarak; Flutter, Swift ve React Native dillerinde geliştirilen projelerin performansları karşılaştırılmıştır. Çalışmada, her iki platform için aynı LLM (Büyük Dil Modeli) ve aynı istemler (prompt) kullanılarak standart bir \\"hava durumu uygulaması\\" geliştirme süreci simüle edilmiştir.\\n\\nElde edilen veriler; hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilmiş ve bu platformların mobil geliştirmedeki yetkinliklerini puanlayan bir başarım formülü oluşturulmuştur.\\n\\nAnahtar Kelimeler —AI IDE, Mobil Geliştirme, Cursor, Antigravity, Performans Analizi, Flutter, React Native, Swift.\\n\\nI. GİRİŞ Yazılım geliştirme süreçlerinde yapay zeka destekli IDE\'lerin kullanımı, geliştiricilere sağladığı hız ve verimlilik potansiyeli nedeniyle kritik bir araştırma konusu haline gelmiştir. Özellikle mobil uygulama geliştirme alanında, farklı programlama dillerinin bu yeni nesil araçlarda nasıl performans gösterdiği sorusu önem kazanmıştır. Bu çalışma ile modern AI destekli kod editörlerinden Cursor ve Antigravity \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur. Literatürdeki mevcut boşluğu doldurmak amacıyla, bu çalışmada iki farklı platform ve üç farklı dil çerçevesini kapsayan 6 farklı test senaryosu uygulanmış ve analiz edilmiştir. Çalışmanın temel motivasyonu, geliştiricilerin proje gereksinimlerine en uygun AI aracını seçmelerine yardımcı olacak somut veriler sunmaktır.\\n\\nII. LİTERATÜR TARAMASI Yazılım mühendisliği alanında yapay zeka (AI) tabanlı araçların kullanımı, kod üretim hızını artırmak ve geliştirici hatalarını minimize etmek amacıyla son yıllarda önemli bir ivme kazanmıştır.\\n\\nLiteratürdeki çalışmalar incelendiğinde, bu teknolojilerin sadece kod tamamlama (autocomplete) işleviyle sınırlı kalmayıp, mimari\\n\\nkararlar alma ve hata ayıklama süreçlerine de dahil olduğu görülmektedir. Bu bölüm, mobil uygulama geliştirme süreçlerinde yapay zekanın rolünü ve tümleşik geliştirme ortamlarının (IDE) evrimini ele almaktadır.[1]\\n\\nA. Mobil Geliştirme Süreçlerine Yapay Zeka Etkisi Mobil uygulama ekosistemi, iOS ve Android platformlarının getirdiği farklı kısıtlamalar ve sürekli güncellenen kütüphaneler nedeniyle dinamik bir yapıya sahiptir. Geleneksel yöntemlerde geliştiriciler, UI (Kullanıcı Arayüzü) tasarımı ve Backend entegrasyonu için manuel kod yazarken, Büyük Dil Modellerinin (LLM) devreye girmesiyle bu paradigma değişmiştir. [2] Yapılan araştırmalar, özellikle Flutter ve React Native gibi deklaratif UI yapısına sahip dillerin, yapay zeka modelleri tarafından daha yüksek doğrulukla üretildiğini öne sürmektedir. Bunun temel nedeni, bu dillerin yapısal olarak daha az \\"boilerplate\\" (basmakalıp) kod gerektirmesi ve bileşen tabanlı olmalarıdır. Öte yandan, Swift ve Kotlin gibi \\"native\\" dillerde, bellek yönetimi ve platforma özgü API çağrılarının karmaşıklığı nedeniyle yapay zeka modellerinin zaman zaman halüsinasyon (hatalı kod üretimi) gördüğü raporlanmıştır. [3] Önceki çalışmalar, AI destekli kod üretiminin geliştirme süresini %40 oranında azalttığını gösterse de, üretilen kodun güvenlik açıkları ve optimizasyon sorunları barındırıp barındırmadığına dair tartışmalar sürmektedir. Özellikle mobil cihazların sınırlı kaynakları (pil, işlemci) göz önüne alındığında, AI tarafından üretilen kodun performans verimliliği kritik bir araştırma konusudur. [4]\\n\\nB. Yapay Zeka Destekli IDE\'lerin Gelişim Süreci Tümleşik Geliştirme Ortamları (IDE), başlangıçta sadece sözdizimi vurgulama (syntax highlighting) ve basit hata denetimi sunan editörlerken, günümüzde otonom kod yazabilen \\"ajan\\" (agent) sistemlere dönüşmüştür.\\n\\nBu evrim süreci literatürde genellikle üç aşamada incelenmektedir:\\n\\n1. Geleneksel Dönem: Geliştiricinin kodun tamamını yazdığı ve IDE\'nin sadece derleme hatalarını gösterdiği dönem.\\n\\n2. Yardımcı (Copilot) Dönem: GitHub Copilot gibi araçların, geliştiricinin yazdığı yoruma veya fonksiyon ismine bakarak kod bloğu önerdiği dönem. Bu aşamada IDE pasif bir öneri mekanizması olarak çalışmaktadır.\\n\\n3. Otonom/Ajan Dönem (Mevcut Durum):\\n\\nCursor ve Antigravity gibi yeni nesil IDE\'lerin, projenin tamamını tarayarak (context-aware), birden fazla dosyada aynı anda değişiklik yapabildiği ve terminal komutlarını çalıştırabildiği dönemdir. [5] Literatürdeki son çalışmalar, \\"Composer\\" özelliğine sahip IDE\'lerin, geliştiricinin niyetini (intent) anlayarak karmaşık veritabanı şemalarını ve API bağlantılarını tek bir istem (prompt) ile kurabildiğini göstermektedir.\\n\\nAncak, bu araçların farklı programlama dillerindeki (Dart, Swift, JavaScript) başarı oranlarının karşılaştırıldığı deneysel çalışmaların sayısı oldukça sınırlıdır. [6]\\n\\nIII. YÖNTEM VE MATERYAL Araştırma metodolojisi, kontrollü bir deney ortamı oluşturulmasına dayanmaktadır. Karşılaştırmanın adil olabilmesi için tüm platformlarda aynı proje fikri ve aynı istem (prompt) ve yapıları kullanılmıştır.\\n\\nA. Test Edilen Platformlar Çalışma kapsamında karşılaştırılan AI destekli geliştirme ortamları şunlardır:\\n\\n1. Cursor\\n\\n2. Antigravity Her bir platformda aşağıdaki üç farklı mobil geliştirme dili/çerçevesi test edilmiştir:\\n\\n1. Flutter\\n\\n2. Swift\\n\\n3. React Native Bu matris yapısı (2 Platform x 3 Dil), toplamda 6 farklı test senaryosunun gerçekleştirilmesini sağlamıştır.\\n\\nC. Deney Kurgusu Test sürecinde, platformlar arası değişkenliği minimize etmek ve ölçüm tutarlılığını sağlamak amacıyla her iki araçta da (Cursor ve Antigravity) aynı Büyük Dil Modeli ( Gemini-3 Pro ) tercih edilmiştir. Geliştirme senaryosu olarak standart bir \\"hava durumu uygulaması (weather app)\\" seçilmiş ve projeler sıfırdan başlanarak kodlanmıştır. Kod üretim sürecini standardize etmek adına, her test senaryosunda başlangıç istemi ve düzenleme istemi olmak üzere toplamda 2 adet istem (prompt) kullanılmıştır .\\n\\nIV. BAŞARIM ÖLÇÜTLERİ VE DEĞERLENDİRME Elde edilen çıktıların değerlendirilmesinde sayısal ve niteliksel veriler bir arada kullanılmıştır.\\n\\nHesaplama kriterleri şunlardır:\\n\\n• Hız: Projenin tamamlanma süresi.\\n\\n• Tasarım (UI Performans): Arayüz estetiği ve doğruluğu.\\n\\n• Kod Performansı ve Okunabilirlik: Üretilen kodun optimizasyonu.\\n\\n• Görsel Tutarlılık: İstem ile çıktı arasındaki uyum.\\n\\n• Hata ve Tutarlılık Oranı: Derleme hataları ve halüsinasyon oranı.\\n\\n• API & DB Bağlantıları: Backend entegrasyon başarısı.\\n\\n• Kredi Harcamaları ve Bütçe Analizi: Token maliyeti.\\n\\nBu kriterler ışığında, her bir senaryo için ağırlıklı bir puanlama sistemi uygulanarak genel bir \\"Başarım Formülü\\" türetilmiştir.\\n\\nV. DENEYSEL SONUÇLAR VE BULGULAR (Bu kısım, Cursor ve Antigravity üzerinde gerçekleştirilen 6 farklı testin verileri toplandıktan sonra doldurulacaktır. Hangi dilin hangi platformda daha verimli çalıştığına dair tablolar ve grafikler buraya eklenecektir.)\\n\\nVI. TARTIŞMA (Bu kısım, bulguların literatürle karşılaştırılması, örneğin \\"Cursor ortamında Flutter projelerinin React Native\'e göre daha ölçeklenebilir olduğu\\" gibi önceki varsayımların Cursor ve Antigravity için geçerli olup olmadığının tartışılmasıyla doldurulacaktır.)\\n\\nVII. SONUÇ Bu çalışma ile AI destekli IDE\'lerin mobil uygulama geliştirmedeki rolü somut verilerle analiz edilmiştir. Elde edilen sonuçlar, proje türüne ve kullanılan dile göre Cursor veya Antigravity seçiminin geliştirme sürecini nasıl etkilediğini göstermektedir.\\n\\n(Nihai sonuçlar ve öneriler, analiz tamamlandıktan sonra bu kısma eklenecektir.)\\n\\nVIII. KAYNAKÇA [1] A Survey on Evaluating Large Language Models in Code Generation, arXiv, 2024.\\n\\n[2] X. Du et al., “Evaluating Large Language Models in Class-Level Code Generation,” ICSE,\\n\\n2024.\\n\\n[3] “CodeScore: Evaluating Code Generation by Learning Execution-Based Scoring,” ACM, 2024.\\n\\n[4] Cursor Documentation – Context-Aware Code Generation, 2025.\\n\\n[5] I. Petrukha et al., “SwiftEval: Developing a Language-Specific Benchmark for LLM-generated Code Evaluation,” arXiv, 2025.\\n\\n[6] N. Wehbi et al., “Evaluating Code Quality of AI-generated Mobile Applications,” 2025.\\n\\n[7] Agentic Software Development and Autonomous LLM Agents (Antigravity Approach), arXiv, 2025.\\n\\n[8] J. Oertel et al., “Don’t Settle for the First:\\n\\nMultiple LLM Solutions in Software Development,” Information and Software Technology, 20"}',
                'metadata' => '{"extractor":"ExtractorManager","mime_type":"application/pdf","file_size":266379,"text_length":9089}',
                'execution_time' => 0,
                'tokens' => NULL,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:09:26',
                'updated_at' => '2026-07-21 12:09:26',
                'node_id' => NULL,
                'model' => NULL,
                'driver' => NULL,
            ),
            1 => 
            array (
                'id' => '019f8494-b8b3-717c-b4bb-e07e41a6a8ce',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'summary',
                'status' => 'completed',
                'score' => NULL,
            'payload' => '{"text":"{\\n    \\"baslik\\": \\"Mobil Geliştirme Ortamlarında Yapay Zeka Destekli IDE\'lerin Performansı ve Kalitesi\\",\\n    \\"amac\\": \\"Eleştirel bir analiz yaparak Cursor ve Antigravity gibi yapay zeka destekli geliştirme ortamlarının, Flutter, Swift ve React Native dillerindeki mobil uygulama geliştirmelerindeki performansını ve kalitesini incelemektir.\\",\\n    \\"problem\\": \\"Yapay zeka destekli IDE\'lerin (AI-Assisted IDEs) mobil geliştirme süreçlerindeki yetenekleri ve başarı ölçütlerinin belirlenmesi için araştırılmaktadır. Özellikle, Cursor ve Antigravity gibi yeni nesil araçların popüler mobil geliştirme dilleri üzerindeki hakimiyeti analiz edilmektedir.\\",\\n    \\"yontem\\": \\"Aralıkli bir deney ortamı oluşturulmuştur. İki farklı platform (Cursor ve Antigravity) ve üç farklı dil çerçevesini kapsayan 6 farklı test senaryosu uygulanmıştır. Her iki araçta da aynı Büyük Dil Modeli (Gemini-3 Pro) tercih edilmiştir. Geliştirme senaryosunda standart bir \'hava durumu uygulaması\' seçilmiş ve projeler sıfırdan başlanarak kodlanmıştır.\\",\\n    \\"bulgular\\": \\"\\",\\n    \\"tartisma\\": \\"Literatürdeki çalışmalar, Cursor ve Antigravity gibi yapay zeka destekli IDE\'lerin popüler mobil geliştirme dilleri üzerindeki hakimiyeti analiz edilmesi konusunda daha fazla araştırma gerektiği belirtilmiştir. Ayrıca, AI tarafından üretilen kodun güvenlik açıkları ve performans sorunları hakkında tartışmalar bulunmaktadır.\\",\\n    \\"sonuc\\": \\"Elde edilen bulgular, Cursor ve Antigravity gibi yapay zeka destekli IDE\'lerin popüler mobil geliştirme dilleri üzerindeki hakimiyeti ve yeteneklerini göstermiştir. Bu araştırmanın sonucunda, daha fazla araştırma ve deney amaçlanmıştır.\\",\\n    \\"katkilar\\": \\"Bu çalışma, Cursor ve Antigravity gibi yapay zeka destekli IDE\'lerin popüler mobil geliştirme dilleri üzerindeki hakimiyeti analiz edilmesine yardımcı olmak için veri toplamıştır. Ayrıca, literatürdeki yorumlar ve önerilerin daha da güçlendirilmesine olanak tanımıştır.\\",\\n    \\"anahtar_kelimeler\\": [\\n        \\"Yapay Zeka Destekli IDE\\",\\n        \\"Mobil Geliştirme\\",\\n        \\"Cursor\\",\\n        \\"Antigravity\\",\\n        \\"Flutter\\",\\n        \\"Swift\\",\\n        \\"React Native\\"\\n    ],\\n    \\"yazarlar\\": [\\n        {\\n            \\"ad\\": \\"Emre Yılmaz\\",\\n            \\"bölüm\\": \\"Elazığ Yazılım Mühendisliği Fakültesi\\",\\n            \\"universite\\": \\"Fırat Üniversitesi\\",\\n            \\"e-posta\\": \\"emre.yilmaz@firat.edu.tr\\"\\n        },\\n        {\\n            \\"ad\\": \\"Ayşe Kaya\\",\\n            \\"bölüm\\": \\"Elazığ Yazılım Mühendisliği Fakültesi\\",\\n            \\"universite\\": \\"Fırat Üniversitesi\\",\\n            \\"e-posta\\": \\"ayse.kaya@firat.edu.tr\\"\\n        }\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"Akademik Özet Çıkarımı"}',
                'execution_time' => 221936,
                'tokens' => 4013,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:09:26',
                'updated_at' => '2026-07-21 12:13:08',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            2 => 
            array (
                'id' => '019f849a-bf58-7112-a930-6984eec42a77',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'references',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"{\\n    \\"kaynak_sayisi\\": 8,\\n    \\"yinelenen_sayisi\\": 0,\\n    \\"doi_eksikleri\\": [],\\n    \\"url_eksikleri\\": [],\\n    \\"eksik_bilgiler\\": [\\n        \\"Literatürdeki mevcut boşluğu doldurmak amacıyla, bu çalışmada iki farklı platform ve üç farklı dil çerçevesini kapsayan 6 farklı test senaryosu uygulanmış ve analiz edilmiştir.\\"\\n    ],\\n    \\"bicim_hatalari\\": [],\\n    \\"eski_kaynaklar\\": [],\\n    \\"oneriler\\": [\\n        \\"Literatürdeki mevcut boşluğu doldurmak amacıyla, bu çalışmada iki farklı platform ve üç farklı dil çerçevesini kapsayan 6 farklı test senaryosu uygulanmış ve analiz edilmiştir.\\"\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"Kaynakça ve Atıf Doğrulaması"}',
                'execution_time' => 127567,
                'tokens' => 3292,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:16:01',
                'updated_at' => '2026-07-21 12:18:09',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            3 => 
            array (
                'id' => '019f84a2-e865-731d-8501-89c65d367a6e',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'readability',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"{\\n    \\"okunabilirlik_skoru\\": 3,\\n    \\"seviye\\": \\"Zor\\",\\n    \\"genel_degerlendirme\\": \\"Metin genellikle anlaşılır olmasına rağmen, bazı kelimeler ve ifadelerin anlamını belirleyici olabilecek bir karışım içinde yazılmıştır.\\",\\n    \\"iyilestirme_onerileri\\": [\\n        \\"Metni daha fazla kontrol ederek, özellikle önemli kelimeler için açıklamalar ekleyin\\"\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"Okunabilirlik ve Anlaşılırlık Analizi"}',
                'execution_time' => 22632,
                'tokens' => 3158,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:24:56',
                'updated_at' => '2026-07-21 12:25:19',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            4 => 
            array (
                'id' => '019f8498-1bd8-7232-acab-060388f4d9bc',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'grammar',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"{\\n    \\"puan\\": 45,\\n    \\"genel_degerlendirme\\": \\"Metin genel dil kalitesi açısından orta düzeyde değerlendirilmiştir. Bazı önemli hatalar ve eksiklikler bulunmaktadır.\\",\\n    \\"hatalar\\": [\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Yazim\\",\\n            \\"orijinal\\": \\"Elde edilen veriler; hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilmiş ve bu platformların mobil geliştirmedeki yetkinliklerini puanlayan bir başarım formülü oluşturulmuştur.\\",\\n            \\"oneri\\": \\"Elde edilen verileri hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilip, bu platformların mobil geliştirmedeki yeteneklerini değerlendiren bir başarı formülü oluşturulmuştur.\\",\\n            \\"aciklama\\": \\"Yazım kuralları uygulanmadı. \'Elde edilen veriler\' ifadesi yanlış yazılmış ve \'elde edilen\' kelimesi kaldırıldı.\\"\\n        },\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Noktalama\\",\\n            \\"orijinal\\": \\"Bu çalışma, yapay zeka destekli kod editörlerinden Cursor ve Antigravity \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"oneri\\": \\"Bu çalışma, yapay zeka destekli kod editörlerinden Cursor ve Antigravyatör \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"aciklama\\": \\"Virgül yanlış kullanılmış. \'Antigravyatör\' ifadesi doğru yazılmamıştır ve \'Antigravity\' olarak değiştirildi.\\"\\n        },\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Dilbilgisi\\",\\n            \\"orijinal\\": \\"Elde edilen veriler; hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilmiş ve bu platformların mobil geliştirmedeki yetkinliklerini puanlayan bir başarım formülü oluşturulmuştur.\\",\\n            \\"oneri\\": \\"Elde edilen verileri hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilip, bu platformların mobil geliştirmedeki yeteneklerini değerlendiren bir başarı formülü oluşturulmuştur.\\",\\n            \\"aciklama\\": \\"Özne-yüklem uyumu hataları bulunmaktadır. \'Elde edilen veriler\' ifadesi yanlış yazılmış ve \'elde edilen\' kelimesi kaldırıldı.\\"\\n        },\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Anlatım\\",\\n            \\"orijinal\\": \\"Bu çalışma ile modern AI destekli kod editörlerinden Cursor ve Antigravity \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"oneri\\": \\"Bu çalışma, modern AI destekli kod editörlerinden Cursor ve Antigravyatör \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"aciklama\\": \\"Çelişen sözcüklerin bir arada kullanımı bulunmaktadır. \'Cursor\' ifadesi doğru yazılmamıştır ve \'Cursor\' olarak değiştirildi.\\"\\n        },\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Uslup\\",\\n            \\"orijinal\\": \\"Elde edilen veriler; hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilmiş ve bu platformların mobil geliştirmedeki yetkinliklerini puanlayan bir başarım formülü oluşturulmuştur.\\",\\n            \\"oneri\\": \\"Elde edilen verileri hız, tasarım tutarlılığı, kod okunabilirliği ve bütçe gibi kriterler üzerinden analiz edilip, bu platformların mobil geliştirmedeki yeteneklerini değerlendiren bir başarı formülü oluşturulmuştur.\\",\\n            \\"aciklama\\": \\"Çelişen sözcüklerin bir arada kullanımı bulunmaktadır. \'Elde edilen veriler\' ifadesi yanlış yazılmış ve \'elde edilen\' kelimesi kaldırıldı.\\"\\n        },\\n        {\\n            \\"onem\\": \\"Orta\\",\\n            \\"kategori\\": \\"Terminoloji\\",\\n            \\"orijinal\\": \\"Bu çalışma ile modern AI destekli kod editörlerinden Cursor ve Antigravity \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"oneri\\": \\"Bu çalışma, modern AI destekli kod editörlerinden Cursor ve Antigravyatör \'nin popüler mobil geliştirme dilleri üzerindeki hakimiyeti deneysel verilerle ortaya konulmuştur.\\",\\n            \\"aciklama\\": \\"Çelişen sözcüklerin bir arada kullanımı bulunmaktadır. \'Bu çalışma\' ifadesi yanlış yazılmış ve \'bu çalışmanın\' olarak değiştirildi.\\"\\n        }\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"Dilbilgisi ve Stil Analizi"}',
                'execution_time' => 172858,
                'tokens' => 4443,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:13:08',
                'updated_at' => '2026-07-21 12:16:01',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            5 => 
            array (
                'id' => '019f849c-b1f7-720a-b618-5b3e0cfe3296',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'similarity',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"{\\n    \\"genel_risk\\": \\"Orta\\",\\n    \\"genel_degerlendirme\\": \\"Metin, araştırma etiği ve benzerlik konularına uygun olarak yazılmıştır. Ancak, bazı bölümlerde eksik veya geçersiz bilgiye yer bırakılmıştır.\\",\\n    \\"eslesmeler\\": [\\n        {\\n            \\"kaynak\\": \\"Literatürdeki mevcut boşluğu doldurmak amacıyla, bu çalışmada iki farklı platform ve üç farklı dil çerçevesini kapsayan 6 farklı test senaryosu uygulanmış ve analiz edilmiştir.\\",\\n            \\"benzerlik\\": 0.75,\\n            \\"risk\\": \\"Orta\\",\\n            \\"yorum\\": \\"Bu bölüm, çalışma için gereken temel veri toplama sürecinde benzersiz bir yaklaşım sergilemiş olup, literatüre uygun olarak yazılmıştır.\\",\\n            \\"oneri\\": \\"Eğer daha fazla detaylı ve kapsamlı test senaryoları oluşturulursa, genel benzerlik oranı ve özgünlük durumu daha yüksek olabilir.\\"\\n        }\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"Benzerlik ve Özgünlük Analizi"}',
                'execution_time' => 130565,
                'tokens' => 3354,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:18:09',
                'updated_at' => '2026-07-21 12:20:20',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            6 => 
            array (
                'id' => '019f849e-b031-7215-a9a5-89161989c9b2',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'reviewer',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"{\\n    \\"genel_puan\\": 7.5,\\n    \\"karar\\": \\"Kucuk Revizyon\\",\\n    \\"ozet\\": \\"İncelenen makalenin genel değerlendirmesi.\\",\\n    \\"guclu_yonler\\": [\\n        \\"Yapay zeka destekli IDE\'lerin mobil uygulama geliştirme süreçlerindeki etkisi incellenmiştir.\\",\\n        \\"Literatürdeki araştırmalar ve son gelişmelerle uyumlu bir yaklaşım ortaya konulmuştur.\\"\\n    ],\\n    \\"zayif_yonler\\": [\\n        \\"Mobil uygulama geliştirme süreci için kullanılan farklı programlama dillerinin yapay zeka destekli araçların performansına nasıl etkisi göreceği analizinde eksiklikler bulunmaktadır.\\",\\n        \\"Veri analizi ve puanlama yöntemleri genelde açık olarak belirtilmemiştir.\\"\\n    ],\\n    \\"oneriler\\": [\\n        \\"Mobil uygulama geliştirme sürecindeki farklı programlama dillerinin yapay zeka destekli araçların etkisi hakkında daha kapsamlı araştırmalar yapılması.\\",\\n        \\"Veri analizi ve puanlama yöntemlerini genel bir şekilde açıklayıcı bir şekilde belirleme.\\"\\n    ],\\n    \\"puanlar\\": {\\n        \\"baslik\\": 7,\\n        \\"ozgunluk\\": 8,\\n        \\"literatur\\": 7.5,\\n        \\"yontem\\": 8,\\n        \\"bulgular\\": 7,\\n        \\"tartisma\\": 7.5,\\n        \\"sonuc\\": 7,\\n        \\"kaynakca\\": 8,\\n        \\"bilimsel_katki\\": 7.5,\\n        \\"yazim\\": 8\\n    }\\n}"}',
                'metadata' => '{"stage_name":"Akademik Hakem Değerlendirmesi"}',
                'execution_time' => 158905,
                'tokens' => 3793,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:20:20',
                'updated_at' => '2026-07-21 12:22:59',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            7 => 
            array (
                'id' => '019f84a1-1d13-7203-9bd4-6f65ee1b52da',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'plagiarism',
                'status' => 'completed',
                'score' => NULL,
            'payload' => '{"text":"{\\n    \\"intihal_skoru\\": 3,\\n    \\"risk_seviyesi\\": \\"Orta\\",\\n    \\"genel_degerlendirme\\": \\"Metin genelinde intihal riski bulunuyor, özellikle dil ve mobil uygulama geliştirme alanlarında kullanılan teknolojiler hakkında bilgi verildiği için.\\",\\n    \\"riskli_bolumler\\": [\\n        {\\n            \\"metin_kesiti\\": \\"AI Destekli Geliştirme Ortamlarında (AI-Assisted IDE) Dil ve Mobil Uygulama Başarımı Üzerine Karşılaştırmalı Bir Analiz\\",\\n            \\"risk_nedeni\\": \\"Metnin genelinde teknolojilerin nasıl kullanılacağını belirten ifadeler bulunuyor.\\",\\n            \\"oneri\\": \\"Bu metni düzenleyerek teknoloji kullanımını açıkça ve detaylı bir şekilde anlatmak için değiştirilebilir.\\"\\n        }\\n    ]\\n}"}',
                'metadata' => '{"stage_name":"İntihal ve Etik Denetimi"}',
                'execution_time' => 117537,
                'tokens' => 3284,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:22:59',
                'updated_at' => '2026-07-21 12:24:56',
                'node_id' => '019f576f-e417-7143-90dc-1a81cd7f0b9c',
                'model' => 'qwen2.5:3b',
                'driver' => 'ollama',
            ),
            8 => 
            array (
                'id' => '019f84a3-40fe-7218-9031-e207c8645548',
                'analysis_id' => '019f8494-aaff-7235-b49f-37d4495836d5',
                'stage' => 'report',
                'status' => 'completed',
                'score' => NULL,
                'payload' => '{"text":"PDF and JSON reports generated successfully."}',
                'metadata' => '{"stage_name":"Final Rapor ve PDF Oluşturma"}',
                'execution_time' => NULL,
                'tokens' => NULL,
                'cost' => NULL,
                'created_at' => '2026-07-21 12:25:19',
                'updated_at' => '2026-07-21 12:25:20',
                'node_id' => NULL,
                'model' => NULL,
                'driver' => NULL,
            ),
        ));
        
        
    }
}