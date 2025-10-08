<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            if (!Schema::hasColumn('files', 'identifier')) {
                $table->string('identifier', 100)->unique()->after('id');
            }
            if (Schema::hasColumn('files', 'paste_id')) {
                $table->unsignedBigInteger('paste_id')->nullable()->change();
            }
            if (!Schema::hasColumn('files', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('paste_id');
            }
            if (!Schema::hasColumn('files', 'views')) {
                $table->integer('views')->default(0)->after('size_bytes');
            }
            if (!Schema::hasColumn('files', 'is_private')) {
                $table->boolean('is_private')->default(false)->after('views');
            }
            if (!Schema::hasColumn('files', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('is_private');
            }
            $table->index(['identifier']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            foreach (['identifier','user_id','views','is_private','expires_at'] as $col) {
                if (Schema::hasColumn('files', $col)) {
                    if ($col === 'user_id') { $table->dropConstrainedForeignId('user_id'); } else { $table->dropColumn($col); }
                }
            }
        });
    }
};


