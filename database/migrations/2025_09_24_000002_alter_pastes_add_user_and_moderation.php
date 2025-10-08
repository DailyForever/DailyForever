<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (!Schema::hasColumn('pastes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('pastes', 'uploader_ip')) {
                $table->string('uploader_ip', 45)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('pastes', 'is_removed')) {
                $table->boolean('is_removed')->default(false)->after('views');
            }
            if (!Schema::hasColumn('pastes', 'removed_reason')) {
                $table->string('removed_reason', 255)->nullable()->after('is_removed');
            }
            if (!Schema::hasColumn('pastes', 'removed_at')) {
                $table->timestamp('removed_at')->nullable()->after('removed_reason');
            }
            if (!Schema::hasColumn('pastes', 'removed_by')) {
                $table->foreignId('removed_by')->nullable()->constrained('users')->nullOnDelete()->after('removed_at');
            }
            if (!Schema::hasColumn('pastes', 'view_limit')) {
                $table->integer('view_limit')->nullable()->after('views');
            }
            $table->index(['user_id']);
            $table->index(['is_removed']);
        });
    }

    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (Schema::hasColumn('pastes', 'removed_by')) {
                $table->dropConstrainedForeignId('removed_by');
            }
            if (Schema::hasColumn('pastes', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            foreach (['uploader_ip','is_removed','removed_reason','removed_at','view_limit'] as $col) {
                if (Schema::hasColumn('pastes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};


