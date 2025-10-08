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
        if (Schema::hasTable('support_reports')) {
            Schema::table('support_reports', function (Blueprint $table) {
                if (Schema::hasColumn('support_reports', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('support_reports', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_reports', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
        });
    }
};
