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
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('analysis_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('stage');
            // extract
            // summary
            // grammar
            // references
            // similarity
            // reviewer
            // plagiarism
            // readability
            // report

            $table->string('status')->default('completed');

            $table->decimal('score', 5, 2)->nullable();

            $table->json('payload')->nullable();

            $table->json('metadata')->nullable();

            $table->unsignedInteger('execution_time')->nullable();

            $table->unsignedInteger('tokens')->nullable();

            $table->decimal('cost', 12, 6)->nullable();

            $table->timestamps();

            $table->index('stage');

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
