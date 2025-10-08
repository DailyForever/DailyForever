<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (!Schema::hasColumn('pastes', 'kem_wrapped_key')) {
                $table->binary('kem_wrapped_key')->nullable()->after('kem_ct');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (Schema::hasColumn('pastes', 'kem_wrapped_key')) {
                $table->dropColumn('kem_wrapped_key');
            }
        });
    }
};
