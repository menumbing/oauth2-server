<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

use function Hyperf\Config\config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100)->index();
            $table->boolean('revoked')->index();
            $table->dateTime('expires_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('access_token_id')->references('id')->on('oauth_access_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_refresh_tokens');
    }

    public function getConnection(): string
    {
        return config('oauth2-server.database.connection');
    }
};
