<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('webpush.database_connection');
        $schema = $connection ? Schema::connection($connection) : Schema::connection(Schema::getDefaultConnection());

        if (! $schema->hasTable(config('webpush.table_name', 'push_subscriptions'))) {
            $schema->create(config('webpush.table_name', 'push_subscriptions'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuidMorphs('subscribable', 'push_subscriptions_subscribable_morph_idx');
                $table->string('endpoint', 500)->unique();
                $table->string('public_key')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('content_encoding')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('webpush.database_connection');
        $schema = $connection ? Schema::connection($connection) : Schema::connection(Schema::getDefaultConnection());

        $schema->dropIfExists(config('webpush.table_name', 'push_subscriptions'));
    }
};
