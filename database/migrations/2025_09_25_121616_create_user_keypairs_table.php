<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_keypairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key_id', 64)->unique(); // Unique identifier for this keypair
            $table->binary('public_key'); // Kyber public key (binary)
            $table->binary('secret_key'); // Kyber secret key (encrypted with user's master key)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->index(['user_id', 'is_active']);
            $table->index(['key_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_keypairs');
    }
};