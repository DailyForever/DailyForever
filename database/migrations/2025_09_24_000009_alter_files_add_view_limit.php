<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            if (!Schema::hasColumn('files', 'view_limit')) {
                $table->integer('view_limit')->nullable()->after('views');
            }
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            if (Schema::hasColumn('files', 'view_limit')) {
                $table->dropColumn('view_limit');
            }
        });
    }
};


