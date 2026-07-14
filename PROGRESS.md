# AI Analyzer - Development Roadmap

> Her sprint tamamlandığında proje çalışır durumda olmalıdır. Yeni özellikler mevcut mimariyi bozmadan eklenmelidir.

---

# Sprint 1 — Proje Altyapısı

## Oluşturulacak Klasörler

* [x] app/Actions
* [x] app/AI
* [x] app/Contracts
* [x] app/DTO
* [x] app/Enums
* [x] app/Exceptions
* [x] app/Jobs
* [x] app/Services
* [x] app/Support
* [x] app/ValueObjects

---

# Sprint 2 — Domain

## Enumlar

* [x] SubmissionStatus
* [x] AnalysisStatus
* [x] AnalysisStage
* [x] MediaType

## Modeller

* [x] Submission
* [x] Media
* [x] Analysis
* [x] AnalysisResult
* [x] Setting

## Migrationlar

* [x] submissions
* [x] media
* [x] analyses
* [x] analysis_results
* [x] settings

## İlişkiler

* [x] User → Submissions
* [x] Submission → Media
* [x] Submission → Analyses
* [x] Analysis → AnalysisResults
* [x] Analysis → Media

---

# Sprint 3 — Submission Pipeline

## Action

* [x] CreateSubmissionAction

## Service

* [x] SubmissionService
* [x] MediaService

## İş Akışı

* [x] Dosya doğrulama
* [x] Submission oluştur
* [x] Media oluştur
* [x] Status = Pending

---

# Sprint 4 — Filament

## Resource

* [x] SubmissionResource

## Sayfalar

* [x] List
* [x] Create
* [x] Edit
* [x] View

## Form

* [x] Başlık
* [x] Açıklama
* [x] Dosya yükleme

## Table

* [x] Başlık
* [x] Durum
* [x] Oluşturulma Tarihi

---

# Sprint 5 — Analysis

## Action

* [x] StartAnalysisAction

## Service

* [x] AnalysisService

## İş Akışı

* [x] Analysis oluştur
* [x] Status = Pending

---

# Sprint 6 — Queue

## Joblar

* [x] StartAnalysisJob
* [x] ExtractTextJob

## Queue

* [x] Redis Queue
* [x] Queue Worker

---

# Sprint 7 — AI Provider

## Interface

* [x] AIProviderInterface

## Providerlar

* [x] OllamaProvider

## Config

* [x] config/ai.php

---

# Sprint 8 — Prompt Sistemi

## resources/prompts

* [x] summary.md
* [x] grammar.md
* [x] reviewer.md
* [x] references.md
* [x] similarity.md

## Service

* [x] PromptService

---

# Sprint 9 — AI Pipeline

## Stage

* [x] Extract
* [x] Summary
* [x] Grammar
* [x] References
* [x] Similarity
* [x] Reviewer

## AnalysisResult

Her Stage sonunda kayıt oluştur.

---

# Sprint 10 — Report

## Service

* [x] ReportService

## Çıktılar

* [x] JSON
* [x] PDF

---

# Sprint 11 — Dashboard

## Widgetlar

* [ ] Toplam Submission
* [ ] Toplam Analysis
* [ ] Başarılı Analysis
* [ ] Başarısız Analysis
* [ ] En Çok Kullanılan Model

---

# Sprint 12 — Settings

## Resource

* [ ] SettingResource

## Ayarlar

* [ ] Default Provider
* [ ] Default Model
* [ ] Temperature
* [ ] Max Tokens
* [ ] Storage Disk

---

# Sprint 13 — Loglama

* [ ] AI İstek Logları
* [ ] Queue Logları
* [ ] Exception Logları

---

# Sprint 14 — Testler

## Feature

* [ ] Submission Test
* [ ] Upload Test
* [ ] Analysis Test

## Unit

* [ ] SubmissionService
* [ ] MediaService
* [ ] AnalysisService
* [ ] PromptService

---

# Sprint 15 — Son Dokunuşlar

* [ ] Dashboard iyileştirmeleri
* [x] Bildirimler (Soft Delete eklendi)
* [ ] Dosya önizleme
* [ ] Rapor indirme
* [ ] Kod temizliği
* [ ] PHPStan
* [ ] Laravel Pint
* [ ] README güncellemesi

---

# Geliştirme Kuralları

Her sprint için aşağıdaki sıra izlenecektir:

1. Migration
2. Model
3. Enum
4. Relationship
5. Action
6. Service
7. Job (gerekiyorsa)
8. Filament Resource
9. Test
10. Refactor

Hiçbir sprintte business logic Controller veya Filament Resource içine yazılmayacaktır. Tüm iş mantığı Action, Service ve Job katmanlarında yer alacaktır.
