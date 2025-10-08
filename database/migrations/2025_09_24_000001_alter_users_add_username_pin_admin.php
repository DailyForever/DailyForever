<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 64)->unique()->after('id');
            }
            // Email becomes nullable for no-email accounts
            if (Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->change();
            }
            if (!Schema::hasColumn('users', 'pin_hash')) {
                $table->string('pin_hash', 255)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'recovery_token')) {
                $table->string('recovery_token', 128)->nullable()->after('pin_hash');
            }
            if (!Schema::hasColumn('users', 'recovery_token_expires_at')) {
                $table->timestamp('recovery_token_expires_at')->nullable()->after('recovery_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'pin_hash')) {
                $table->dropColumn('pin_hash');
            }
            if (Schema::hasColumn('users', 'recovery_token')) {
                $table->dropColumn('recovery_token');
            }
            if (Schema::hasColumn('users', 'recovery_token_expires_at')) {
                $table->dropColumn('recovery_token_expires_at');
            }
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            // Do not revert email to non-nullable automatically to avoid data loss
        });
    }
};


