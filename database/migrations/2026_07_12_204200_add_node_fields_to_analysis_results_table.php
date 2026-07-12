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
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->foreignUuid('node_id')
                ->nullable()
                ->after('analysis_id')
                ->constrained('nodes')
                ->nullOnDelete();

            $table->string('model')
                ->nullable()
                ->after('node_id');

            $table->string('driver')
                ->nullable()
                ->after('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->dropForeign(['node_id']);
            $table->dropColumn(['node_id', 'model', 'driver']);
        });
    }
};
