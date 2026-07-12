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
        Schema::create('stage_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stage')->unique();
            $table->string('model');

            $table->foreignUuid('node_id')
                ->nullable()
                ->constrained('nodes')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_routes');
    }
};
