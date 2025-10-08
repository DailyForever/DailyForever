<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (!Schema::hasColumn('pastes', 'kem_alg')) {
                $table->string('kem_alg', 32)->nullable()->after('is_private');
            }
            if (!Schema::hasColumn('pastes', 'kem_kid')) {
                $table->string('kem_kid', 64)->nullable()->after('kem_alg');
            }
            if (!Schema::hasColumn('pastes', 'kem_ct')) {
                $table->binary('kem_ct')->nullable()->after('kem_kid');
            }
            if (!Schema::hasColumn('pastes', 'recipient_id')) {
                $table->unsignedBigInteger('recipient_id')->nullable()->after('kem_ct');
            }
            $table->index(['recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (Schema::hasColumn('pastes', 'recipient_id')) {
                $table->dropColumn('recipient_id');
            }
            foreach (['kem_alg','kem_kid','kem_ct'] as $col) {
                if (Schema::hasColumn('pastes', $col)) { $table->dropColumn($col); }
            }
        });
    }
};


