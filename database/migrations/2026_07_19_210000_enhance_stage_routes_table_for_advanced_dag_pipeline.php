<?php

declare(strict_types=1);

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
            $table->json('dependencies')->nullable()->after('description');
            $table->string('on_failure')->default('skip')->after('dependencies'); // skip, fail_pipeline
            $table->unsignedInteger('max_retries')->default(3)->after('on_failure');
            $table->unsignedInteger('timeout_seconds')->default(120)->after('max_retries');
            $table->decimal('temperature', 3, 2)->nullable()->after('timeout_seconds');
            $table->unsignedInteger('max_tokens')->nullable()->after('temperature');
            $table->string('output_format')->default('json')->after('max_tokens'); // json, text, markdown
            $table->json('config')->nullable()->after('output_format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_routes', function (Blueprint $table) {
            $table->dropColumn([
                'dependencies',
                'on_failure',
                'max_retries',
                'timeout_seconds',
                'temperature',
                'max_tokens',
                'output_format',
                'config',
            ]);
        });
    }
};
