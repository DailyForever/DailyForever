<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class BackupCodeService
{
    /**
     * Generate a new backup code
     */
    public static function generate(): string
    {
        // Use cryptographically secure RNG. 16 hex chars (8 bytes) grouped for readability if desired by caller.
        // Keep return format backward compatible (uppercase, no separators).
        return strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Encrypt a backup code for storage
     */
    public static function encrypt(string $backupCode): string
    {
        return Crypt::encryptString($backupCode);
    }

    /**
     * Decrypt a backup code from storage
     */
    public static function decrypt(string $encryptedBackupCode): string
    {
        return Crypt::decryptString($encryptedBackupCode);
    }

    /**
     * Verify a backup code against the encrypted version
     */
    public static function verify(string $providedCode, string $encryptedStoredCode): bool
    {
        try {
            $decryptedCode = self::decrypt($encryptedStoredCode);
            return hash_equals($decryptedCode, $providedCode);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate and encrypt a backup code
     */
    public static function generateAndEncrypt(): array
    {
        $backupCode = self::generate();
        $encryptedCode = self::encrypt($backupCode);
        
        return [
            'code' => $backupCode,
            'encrypted' => $encryptedCode
        ];
    }
}
