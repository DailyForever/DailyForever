<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paste_id')->constrained('pastes')->cascadeOnDelete();
            $table->string('original_filename', 255)->nullable();
            $table->string('mime_type', 127)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('storage_path', 255);
            $table->timestamps();

            $table->index(['paste_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};


