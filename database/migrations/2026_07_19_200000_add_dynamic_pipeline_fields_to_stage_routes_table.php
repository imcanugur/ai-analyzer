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
        Schema::table('stage_routes', function (Blueprint $table) {
            $table->string('name')->nullable()->after('stage');
            $table->text('description')->nullable()->after('name');
            $table->longText('prompt_template')->nullable()->after('description');
            $table->text('system_prompt')->nullable()->after('prompt_template');
            $table->integer('sort_order')->default(0)->after('node_id');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_routes', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'description',
                'prompt_template',
                'system_prompt',
                'sort_order',
                'is_active',
            ]);
        });
    }
};
