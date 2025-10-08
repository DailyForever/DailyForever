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
            // ZKP-specific fields
            $table->json('zkp_proof')->nullable()->comment('Zero-knowledge proof data for authentication');
            $table->boolean('zkp_enabled')->default(false)->comment('Whether ZKP authentication is enabled for this user');
            $table->timestamp('zkp_proof_created_at')->nullable()->comment('When the ZKP proof was created');
            $table->timestamp('zkp_last_login_at')->nullable()->comment('Last ZKP login timestamp');
            
            // Add index for performance
            $table->index('zkp_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['zkp_enabled']);
            $table->dropColumn([
                'zkp_proof',
                'zkp_enabled', 
                'zkp_proof_created_at',
                'zkp_last_login_at'
            ]);
        });
    }
};
