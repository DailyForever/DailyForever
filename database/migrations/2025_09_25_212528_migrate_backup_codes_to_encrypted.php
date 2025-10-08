<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Services\BackupCodeService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing backup codes from hashed to encrypted format
        $users = User::whereNotNull('backup_code_hash')->get();
        
        foreach ($users as $user) {
            // Generate a new backup code and encrypt it
            $backupCodeData = BackupCodeService::generateAndEncrypt();
            $user->backup_code_hash = $backupCodeData['encrypted'];
            $user->save();
            
            // Log the new backup code for the user
            \Log::info('Migrated backup code for user: ' . $user->username . ' - New code: ' . $backupCodeData['code']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed as we're changing the format
        // Users would need to generate new backup codes
    }
};