<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            if (!Schema::hasColumn('files', 'kem_alg')) {
                $table->string('kem_alg', 32)->nullable()->after('iv');
            }
            if (!Schema::hasColumn('files', 'kem_kid')) {
                $table->string('kem_kid', 64)->nullable()->after('kem_alg');
            }
            if (!Schema::hasColumn('files', 'kem_ct')) {
                // Store raw ciphertext (binary) for KEM encapsulated key
                $table->binary('kem_ct')->nullable()->after('kem_kid');
            }
            if (!Schema::hasColumn('files', 'kem_wrapped_key')) {
                // Store wrapped AES key (binary): [salt(32)][iv(12)][ciphertext]
                $table->binary('kem_wrapped_key')->nullable()->after('kem_ct');
            }
            if (!Schema::hasColumn('files', 'recipient_id')) {
                $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            }
            $table->index(['kem_kid']);
            $table->index(['recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            if (Schema::hasColumn('files', 'recipient_id')) {
                $table->dropConstrainedForeignId('recipient_id');
            }
            foreach (['kem_alg','kem_kid','kem_ct','kem_wrapped_key'] as $col) {
                if (Schema::hasColumn('files', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
