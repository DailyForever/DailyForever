<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prekeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kid', 64); // key id
            $table->string('alg', 32)->default('ML-KEM-768');
            $table->binary('public_key');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'kid']);
            $table->index(['user_id', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prekeys');
    }
};


