<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Per-user SRP group size (e.g., 1024 or 2048). Nullable for legacy users.
            $table->unsignedSmallInteger('srp_group_bits')->nullable()->after('srp_verifier');
            $table->index('srp_group_bits');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['srp_group_bits']);
            $table->dropColumn('srp_group_bits');
        });
    }
};
