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
        // Add rotation tracking fields to user_keypairs table
        Schema::table('user_keypairs', function (Blueprint $table) {
            // First add algorithm column if it doesn't exist
            if (!Schema::hasColumn('user_keypairs', 'algorithm')) {
                $table->string('algorithm', 32)->default('ML-KEM-512')->after('secret_key');
            }
            
            if (!Schema::hasColumn('user_keypairs', 'rotation_from')) {
                $table->string('rotation_from', 64)->nullable()->after('algorithm');
                $table->string('rotation_to', 64)->nullable()->after('rotation_from');
                $table->string('rotation_status', 32)->nullable()->after('rotation_to');
                $table->timestamp('rotation_initiated_at')->nullable()->after('rotation_status');
            }
        });
        
        // Create key rotation audit table
        Schema::create('key_rotation_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 50);
            $table->string('old_key_id', 64)->nullable();
            $table->string('new_key_id', 64)->nullable();
            $table->string('reason')->nullable();
            $table->string('urgency', 20)->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('old_key_id');
            $table->index('new_key_id');
            $table->index('created_at');
        });
        
        // Create random validation metrics table
        Schema::create('random_validation_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50);
            $table->float('entropy_score');
            $table->float('distribution_score');
            $table->float('pattern_score');
            $table->float('overall_score');
            $table->json('issues')->nullable();
            $table->boolean('passed');
            $table->integer('sample_size');
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('passed');
            $table->index('overall_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_keypairs', function (Blueprint $table) {
            $table->dropColumn([
                'algorithm',
                'rotation_from',
                'rotation_to', 
                'rotation_status',
                'rotation_initiated_at'
            ]);
        });
        
        Schema::dropIfExists('key_rotation_audits');
        Schema::dropIfExists('random_validation_metrics');
    }
};
