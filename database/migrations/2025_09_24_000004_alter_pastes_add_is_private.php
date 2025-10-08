<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (!Schema::hasColumn('pastes', 'is_private')) {
                $table->boolean('is_private')->default(false)->after('view_limit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (Schema::hasColumn('pastes', 'is_private')) {
                $table->dropColumn('is_private');
            }
        });
    }
};


