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
        Schema::create('nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('driver')->default('ollama');
            $table->string('endpoint');
            $table->string('api_key')->nullable();
            $table->string('status')->default('unknown'); // online, offline, unknown
            $table->json('capabilities')->nullable(); // array of models
            $table->unsignedInteger('weight')->default(1);
            $table->unsignedInteger('priority')->default(1);
            $table->unsignedInteger('active_connections')->default(0);
            $table->timestamp('last_health_check_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
