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
        Schema::create('pastes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 100)->unique();
            $table->text('encrypted_content');
            $table->string('iv', 100);
            $table->timestamp('expires_at')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
            
            $table->index(['identifier']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pastes');
    }
};
