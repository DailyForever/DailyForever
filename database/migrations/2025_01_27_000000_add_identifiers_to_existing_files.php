<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safeguard: skip if table or column does not exist yet (e.g., test env migration order)
        if (!Schema::hasTable('files') || !Schema::hasColumn('files', 'identifier')) {
            return;
        }
        // Add identifiers to existing files that don't have them
        $filesWithoutIdentifiers = File::whereNull('identifier')->get();
        foreach ($filesWithoutIdentifiers as $file) {
            $file->identifier = File::generateIdentifier();
            $file->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it generates random identifiers
        // that cannot be reliably reverted
    }
};
