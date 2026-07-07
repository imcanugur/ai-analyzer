<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('submission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');
            // document
            // image
            // audio
            // video
            // source-code

            $table->string('category')->nullable();
            // academic
            // plagiarism
            // grammar
            // legal
            // resume
            // medical

            $table->string('provider')->nullable();
            // ollama
            // openai
            // anthropic
            // google

            $table->string('engine')->nullable();
            // llm
            // embedding
            // ocr
            // speech
            // vision

            $table->string('model')->nullable();
            // gemma4
            // qwen3
            // whisper
            // bge-m3

            $table->string('version')->nullable();

            $table->string('status')->default('pending');
            // pending
            // queued
            // processing
            // completed
            // failed
            // cancelled

            $table->json('config')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
