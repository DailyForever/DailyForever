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
        Schema::table('users', function (Blueprint $table) {
            // Security questions
            $table->string('security_question_1')->nullable();
            $table->string('security_answer_1_hash')->nullable();
            $table->string('security_question_2')->nullable();
            $table->string('security_answer_2_hash')->nullable();
            
            // Backup code
            $table->string('backup_code_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'security_question_1',
                'security_answer_1_hash',
                'security_question_2',
                'security_answer_2_hash',
                'backup_code_hash'
            ]);
        });
    }
};