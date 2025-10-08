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
        Schema::create('support_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['dmca', 'abuse', 'general', 'security', 'appeal']);
            $table->string('subject');
            $table->text('description');
            $table->string('email')->nullable();
            $table->string('paste_identifier')->nullable();
            $table->text('copyright_work')->nullable();
            $table->text('authorization')->nullable();
            $table->text('contact_info')->nullable();
            $table->string('violation_type')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['status', 'created_at']);
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_reports');
    }
};