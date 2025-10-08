<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            $table->string('password_hash')->nullable()->after('recipient_id');
            $table->string('password_hint')->nullable()->after('password_hash');
        });
        Schema::table('files', function (Blueprint $table) {
            $table->string('password_hash')->nullable()->after('iv');
            $table->string('password_hint')->nullable()->after('password_hash');
        });
    }

    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            $table->dropColumn(['password_hash', 'password_hint']);
        });
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['password_hash', 'password_hint']);
        });
    }
};



