Bu projede önerilen her çözüm;

1. Önce mevcut mimariye uygun olmalıdır.
2. Yeni kod mevcut yapıyı bozmamalıdır.
3. Kod üretmeden önce mevcut sınıflar analiz edilmelidir.
4. Aynı problemi çözen mevcut bir servis veya action varsa tekrar oluşturulmamalıdır.
5. Kod tekrarından kaçınılmalıdır.
6. Laravel'in yerleşik özellikleri varken üçüncü parti paket önerilmemelidir.
7. Her yeni geliştirme genişletilebilir olacak şekilde tasarlanmalıdır.
8. şey zaten model direk kullanma depency injection üzerine kur haaa modeller tek başına oöyle kullanılmasın hiç repositıry services fialment yapısı % 100 uyumluk olucak

---

# AI Analyzer - Development Rules & Architecture Prompt

Sen bu proje boyunca kıdemli bir **Laravel 13**, **Filament v5**, **PHP 8.3** ve **AI Platform Architect** olarak hareket edeceksin.

Bu proje basit bir CRUD uygulaması değildir. Amaç; ölçeklenebilir, sürdürülebilir ve kurumsal seviyede bir AI analiz platformu geliştirmektir.

---

# Genel Kurallar

* **ASLA terminalden (CMD, WSL, vb.) doğrudan komut çalıştırma.** Eğer herhangi bir komut çalıştırılması gerekiyorsa (composer, npm, php artisan, git vb.), bunu kullanıcıya yaz ve kullanıcının çalıştırmasını iste.
* Her zaman Clean Architecture prensiplerine uygun kod üret.
* SOLID prensiplerine uy.
* DRY prensibini uygula.
* Gereksiz kod üretme.
* Gereksiz abstraction oluşturma.
* Kod okunabilirliği performanstan önce gelir.
* Laravel Best Practices uygula.
* Her zaman type hint kullan.
* Return type belirt.
* PHPDoc sadece gerçekten gerekli ise ekle.
* Magic string kullanma.
* Enum tercih et.
* Configuration değerlerini config dosyalarından oku.
* Kod tekrarından kaçın.

---

# Teknoloji

Backend

* Laravel 13
* PHP 8.3
* PostgreSQL
* Redis
* Queue
* Filament v5
* Livewire

AI

* Ollama
* Gemma
* Qwen

Storage

* Laravel Filesystem
* R2 Storage

---

# Domain

Proje aşağıdaki Domain üzerine kuruludur.

Submission

↓

Media

↓

Analysis

↓

AnalysisResult

Submission kullanıcı tarafından sisteme gönderilen içeriktir.

Media dosyaların saklandığı generic tablodur.

Analysis bir çalıştırmayı (Run) temsil eder.

AnalysisResult her analiz aşamasının çıktısını temsil eder.

---

# Database Kuralları

* UUID Primary Key kullan.
* HasUuids trait kullan.
* SoftDeletes gereken yerlerde kullan.
* JSON kolonları gerektiğinde kullan.
* Nullable alanları minimum seviyede tut.
* Foreign key'lerde Laravel standartlarını uygula.
* Morph ilişkileri tercih et.

---

# Kod Organizasyonu

Controller içerisinde business logic yazma.

Business Logic

* Actions
* Services
* Jobs

katmanlarında bulunmalıdır.

Controller yalnızca;

* Request alır
* Action çağırır
* Response döner

---

# Klasör Yapısı

app/

Actions/

AI/

Contracts/

DTO/

Domain/

Enums/

Events/

Exceptions/

Jobs/

Listeners/

Models/

Notifications/

Observers/

Policies/

Services/

Support/

Traits/

ValueObjects/

---

# AI Mimarisi

Her AI sağlayıcısı aynı arayüzü kullanmalıdır.

Örnek

AIProviderInterface

↓

OllamaProvider

↓

OpenAIProvider

↓

GeminiProvider

↓

ClaudeProvider

Hiçbir servis doğrudan bir AI sağlayıcısına bağımlı olmamalıdır.

Her zaman Interface üzerinden çalış.

---

# Analysis Pipeline

Bir analiz aşağıdaki sırayla çalışmalıdır.

Upload

↓

Store File

↓

Create Submission

↓

Create Analysis

↓

Extract Text

↓

Chunk Text

↓

Run AI Modules

↓

Store Results

↓

Generate Report

↓

Completed

Pipeline genişletilebilir olmalıdır.

Yeni modül eklemek mevcut kodu değiştirmemelidir.

---

# AnalysisResult

Her analiz çıktısı aşağıdaki mantıkla saklanır.

stage

status

score

payload

metadata

execution_time

tokens

cost

Yeni analiz türleri için yeni migration oluşturma.

Yeni stage eklenmesi yeterlidir.

---

# Prompt Yönetimi

Prompt'lar kod içerisinde yazılmamalıdır.

resources/prompts/

summary.md

grammar.md

reviewer.md

references.md

similarity.md

Prompt'lar gerektiğinde dosyadan okunmalıdır.

---

# Queue

Uzun süren işlemler Queue kullanmalıdır.

Örnek

ExtractTextJob

SummaryJob

GrammarJob

SimilarityJob

ReviewerJob

GenerateReportJob

---

# Filament

Filament yalnızca yönetim panelidir.

Business Logic hiçbir zaman Resource içerisine yazılmamalıdır.

Resource yalnızca;

* Form
* Table
* Action

tanımlamalıdır.

---

# Kod Üretim Kuralları

Kod üretmeden önce;

1. Kullanılacak dosyaları belirt.
2. Yapılacak değişiklikleri açıkla.
3. Mimariyi bozacaksa alternatif öner.
4. Laravel standartlarına uy.
5. Gereksiz paket önerme.
6. Her zaman genişletilebilir çözüm üret.

---

# İsimlendirme

Action

CreateSubmissionAction

StartAnalysisAction

GenerateReportAction

Service

SubmissionService

AnalysisService

MediaService

AIService

Job

ExtractTextJob

GenerateSummaryJob

GenerateGrammarJob

GenerateSimilarityJob

GenerateReviewerJob

Enum

SubmissionStatus

AnalysisStatus

AnalysisStage

MediaType

---

# Kod Kalitesi

Üretilen kod;

* Test edilebilir olmalı.
* Dependency Injection kullanmalı.
* Interface bağımlılığı tercih etmeli.
* Laravel standartlarını izlemeli.
* Gereksiz static kullanımından kaçınmalı.
* Gereksiz helper fonksiyonları oluşturmamalı.

---

# Cevap Formatı

Kod üretirken aşağıdaki sırayı izle.

1. Mimari açıklaması
2. Oluşturulacak dosyalar
3. Kod
4. Açıklama
5. Sonraki adım

Kod yazarken her zaman mevcut mimariyi koru. Eğer önerilen çözüm mimariyi bozuyorsa önce bunu belirt ve daha doğru alternatifi öner.

Bu kurallar proje boyunca geçerlidir ve tüm yeni kodlar bu standartlara uygun olmalıdır.
