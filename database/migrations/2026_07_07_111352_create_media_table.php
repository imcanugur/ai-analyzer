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
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->nullableUuidMorphs('mediable');

            $table->string('disk')->default('r2');

            $table->string('path');

            $table->string('url');

            $table->string('mime')->nullable();

            $table->unsignedBigInteger('size')->nullable();

            $table->string('original_name')->nullable();

            $table->string('extension')->nullable();

            $table->string('checksum', 64)->nullable();

            $table->string('type')->nullable();

            $table->json('meta')->nullable();

            $table->boolean('optimized')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
