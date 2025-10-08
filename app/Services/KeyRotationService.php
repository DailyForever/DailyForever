<?php

namespace App\Services;

use App\Models\User;
use App\Models\Paste;
use App\Models\File;
use App\Models\UserKeypair;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class KeyRotationService
{
    // Key rotation policies
    const MAX_KEY_AGE_DAYS = 90; // Maximum age before mandatory rotation
    const MAX_KEY_USES = 10000; // Maximum encryptions per key
    const ROTATION_GRACE_PERIOD_DAYS = 7; // Grace period after rotation trigger
    
    // Security thresholds
    const IV_COLLISION_THRESHOLD = 100; // Number of IV collisions before forced rotation
    const KEY_COMPROMISE_INDICATOR_THRESHOLD = 5; // Failed decryption attempts
    
    /**
     * Check if a key needs rotation
     * 
     * @param string $keyId
     * @param array $metadata
     * @return array ['needs_rotation' => bool, 'reasons' => array, 'urgency' => string]
     */
    public static function checkKeyRotationStatus(string $keyId, array $metadata = []): array
    {
        $reasons = [];
        $urgency = 'none'; // none, recommended, urgent, critical
        
        // Check key age
        $keyAge = $metadata['created_at'] ?? null;
        if ($keyAge) {
            $ageInDays = Carbon::parse($keyAge)->diffInDays(now());
            
            if ($ageInDays > self::MAX_KEY_AGE_DAYS) {
                $reasons[] = "Key age exceeds " . self::MAX_KEY_AGE_DAYS . " days";
                $urgency = 'critical';
            } elseif ($ageInDays > (self::MAX_KEY_AGE_DAYS * 0.8)) {
                $reasons[] = "Key approaching maximum age";
                $urgency = 'urgent';
            } elseif ($ageInDays > (self::MAX_KEY_AGE_DAYS * 0.6)) {
                $reasons[] = "Key age is {$ageInDays} days";
                $urgency = 'recommended';
            }
        }
        
        // Check usage count
        $usageCount = Cache::get("key_usage:{$keyId}", 0);
        if ($usageCount > self::MAX_KEY_USES) {
            $reasons[] = "Key usage exceeds " . self::MAX_KEY_USES . " operations";
            $urgency = 'critical';
        } elseif ($usageCount > (self::MAX_KEY_USES * 0.8)) {
            $reasons[] = "Key approaching maximum usage limit";
            if ($urgency === 'none' || $urgency === 'recommended') {
                $urgency = 'urgent';
            }
        }
        
        // Check IV collisions
        $ivCollisions = Cache::get("key_iv_collisions:{$keyId}", 0);
        if ($ivCollisions > self::IV_COLLISION_THRESHOLD) {
            $reasons[] = "IV collision threshold exceeded ({$ivCollisions} collisions)";
            $urgency = 'critical';
        } elseif ($ivCollisions > (self::IV_COLLISION_THRESHOLD * 0.5)) {
            $reasons[] = "High IV collision rate detected";
            if ($urgency === 'none') {
                $urgency = 'recommended';
            }
        }
        
        // Check compromise indicators
        $failedDecryptions = Cache::get("key_failed_decrypt:{$keyId}", 0);
        if ($failedDecryptions > self::KEY_COMPROMISE_INDICATOR_THRESHOLD) {
            $reasons[] = "Potential key compromise detected (multiple failed decryptions)";
            $urgency = 'critical';
        }
        
        // Check for manual rotation flag
        if (Cache::has("key_rotation_requested:{$keyId}")) {
            $reasons[] = "Manual rotation requested";
            if ($urgency !== 'critical') {
                $urgency = 'urgent';
            }
        }
        
        // Check cryptographic algorithm deprecation
        $algorithm = $metadata['algorithm'] ?? 'AES-GCM';
        if (in_array($algorithm, ['AES-CBC', 'AES-CTR', '3DES', 'RC4'])) {
            $reasons[] = "Using deprecated algorithm: {$algorithm}";
            $urgency = 'critical';
        }
        
        return [
            'needs_rotation' => !empty($reasons),
            'reasons' => $reasons,
            'urgency' => $urgency,
            'keyId' => $keyId,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString()
        ];
    }
    
    /**
     * Perform key rotation for a user
     * 
     * @param User $user
     * @param string $oldKeyId
     * @return array ['success' => bool, 'new_key_id' => string|null, 'error' => string|null]
     */
    public static function rotateUserKeys(User $user, string $oldKeyId = null): array
    {
        DB::beginTransaction();
        
        try {
            // Generate new keypair
            $newKeyId = bin2hex(random_bytes(16));
            $newPublicKey = random_bytes(800);  // ML-KEM-512 public key size
            $newSecretKey = random_bytes(1632); // ML-KEM-512 secret key size
            
            // Store new keypair
            $newKeypair = UserKeypair::create([
                'user_id' => $user->id,
                'key_id' => $newKeyId,
                'public_key' => $newPublicKey,
                'secret_key' => $newSecretKey,
                'algorithm' => 'ML-KEM-512',
                'is_active' => false, // Not active until rotation complete
                'created_at' => now(),
                'rotation_from' => $oldKeyId,
                'rotation_status' => 'pending'
            ]);
            
            // If there's an old key, mark it for deprecation
            if ($oldKeyId) {
                UserKeypair::where('user_id', $user->id)
                    ->where('key_id', $oldKeyId)
                    ->update([
                        'rotation_to' => $newKeyId,
                        'rotation_initiated_at' => now(),
                        'expires_at' => now()->addDays(self::ROTATION_GRACE_PERIOD_DAYS)
                    ]);
                
                // Log rotation event
                self::logRotationEvent($user->id, $oldKeyId, $newKeyId, 'initiated');
            }
            
            // Schedule re-encryption of existing content
            self::scheduleReEncryption($user->id, $oldKeyId, $newKeyId);
            
            // Activate new key
            $newKeypair->update([
                'is_active' => true,
                'rotation_status' => 'active'
            ]);
            
            // Clear rotation flags
            Cache::forget("key_rotation_requested:{$oldKeyId}");
            Cache::forget("key_usage:{$oldKeyId}");
            Cache::forget("key_iv_collisions:{$oldKeyId}");
            Cache::forget("key_failed_decrypt:{$oldKeyId}");
            
            DB::commit();
            
            // Notify user of key rotation
            self::notifyUserOfRotation($user, $oldKeyId, $newKeyId);
            
            return [
                'success' => true,
                'new_key_id' => $newKeyId,
                'old_key_id' => $oldKeyId,
                'grace_period_ends' => now()->addDays(self::ROTATION_GRACE_PERIOD_DAYS)->toISOString()
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Key rotation failed', [
                'user_id' => $user->id,
                'old_key_id' => $oldKeyId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Key rotation failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Schedule re-encryption of content with new key
     */
    protected static function scheduleReEncryption($userId, $oldKeyId, $newKeyId): void
    {
        // Queue job for re-encryption (placeholder - implement with Laravel queues)
        Cache::put(
            "reencryption_pending:{$userId}",
            [
                'old_key_id' => $oldKeyId,
                'new_key_id' => $newKeyId,
                'scheduled_at' => now()->toISOString(),
                'status' => 'pending'
            ],
            now()->addDays(self::ROTATION_GRACE_PERIOD_DAYS)
        );
        
        // In production, dispatch a queued job here
        // dispatch(new ReEncryptUserContentJob($userId, $oldKeyId, $newKeyId));
    }
    
    /**
     * Log key rotation event for audit trail
     */
    protected static function logRotationEvent($userId, $oldKeyId, $newKeyId, $action): void
    {
        $logEntry = [
            'user_id' => $userId,
            'action' => $action,
            'old_key_id' => $oldKeyId,
            'new_key_id' => $newKeyId,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];
        
        // Store in audit log (implement with your logging strategy)
        Log::info('Key rotation event', $logEntry);
        
        // Also store in cache for recent activity monitoring
        $recentRotations = Cache::get('recent_key_rotations', []);
        array_unshift($recentRotations, $logEntry);
        $recentRotations = array_slice($recentRotations, 0, 100); // Keep last 100
        Cache::put('recent_key_rotations', $recentRotations, now()->addDays(30));
    }
    
    /**
     * Notify user of key rotation
     */
    protected static function notifyUserOfRotation(User $user, $oldKeyId, $newKeyId): void
    {
        // Store notification in cache (implement with your notification system)
        Cache::put(
            "user_notification:{$user->id}:rotation",
            [
                'type' => 'key_rotation',
                'message' => 'Your encryption keys have been rotated for security',
                'old_key_id' => substr($oldKeyId, 0, 8) . '...',
                'new_key_id' => substr($newKeyId, 0, 8) . '...',
                'action_required' => false,
                'expires_at' => now()->addDays(self::ROTATION_GRACE_PERIOD_DAYS)->toISOString()
            ],
            now()->addDays(30)
        );
    }
    
    /**
     * Get rotation statistics for monitoring
     */
    public static function getRotationStats(): array
    {
        $recentRotations = Cache::get('recent_key_rotations', []);
        $pendingRotations = [];
        
        // Check all active keys for rotation needs
        $activeKeys = UserKeypair::where('is_active', true)
            ->where('expires_at', null)
            ->get();
        
        foreach ($activeKeys as $keypair) {
            $status = self::checkKeyRotationStatus($keypair->key_id, [
                'created_at' => $keypair->created_at,
                'algorithm' => $keypair->algorithm
            ]);
            
            if ($status['needs_rotation']) {
                $pendingRotations[] = [
                    'user_id' => $keypair->user_id,
                    'key_id' => substr($keypair->key_id, 0, 8) . '...',
                    'urgency' => $status['urgency'],
                    'reasons' => $status['reasons']
                ];
            }
        }
        
        return [
            'recent_rotations_count' => count($recentRotations),
            'recent_rotations' => array_slice($recentRotations, 0, 10),
            'pending_rotations_count' => count($pendingRotations),
            'pending_rotations' => array_slice($pendingRotations, 0, 10),
            'policies' => [
                'max_key_age_days' => self::MAX_KEY_AGE_DAYS,
                'max_key_uses' => self::MAX_KEY_USES,
                'rotation_grace_period_days' => self::ROTATION_GRACE_PERIOD_DAYS
            ],
            'timestamp' => now()->toISOString()
        ];
    }
    
    /**
     * Force immediate key rotation (emergency use)
     */
    public static function forceRotation(User $user, string $reason = 'Emergency rotation'): array
    {
        // Get current active key
        $currentKey = UserKeypair::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        if (!$currentKey) {
            return [
                'success' => false,
                'error' => 'No active key found for user'
            ];
        }
        
        // Log emergency rotation
        Log::warning('Emergency key rotation initiated', [
            'user_id' => $user->id,
            'key_id' => $currentKey->key_id,
            'reason' => $reason
        ]);
        
        // Mark key as compromised
        Cache::put("key_compromised:{$currentKey->key_id}", [
            'reason' => $reason,
            'timestamp' => now()->toISOString()
        ], now()->addDays(90));
        
        // Perform rotation
        return self::rotateUserKeys($user, $currentKey->key_id);
    }
}
