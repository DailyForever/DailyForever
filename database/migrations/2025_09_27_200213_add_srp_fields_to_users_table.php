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
        Schema::table('users', function (Blueprint $table) {
            $table->string('srp_salt')->nullable()->comment('SRP salt for zero-knowledge authentication');
            $table->text('srp_verifier')->nullable()->comment('SRP verifier for zero-knowledge authentication');
            $table->boolean('srp_enabled')->default(false)->comment('Whether SRP authentication is enabled for this user');
            $table->timestamp('srp_enabled_at')->nullable()->comment('When SRP was enabled');
            $table->timestamp('srp_last_login_at')->nullable()->comment('Last SRP login timestamp');
            $table->index('srp_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['srp_salt', 'srp_verifier', 'srp_enabled', 'srp_enabled_at', 'srp_last_login_at']);
        });
    }
};