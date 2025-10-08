<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KeyRotationService;
use App\Services\SecureRandomValidator;
use App\Models\UserKeypair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class KeyRotationController extends Controller
{
    /**
     * Check key rotation status
     * GET /api/keys/rotation-status
     */
    public function checkRotationStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Rate limit status checks
        $key = 'rotation-check:' . $user->id;
        if (!RateLimiter::attempt($key, 10, function() {
            // Callback required by RateLimiter
        }, 60)) {
            return response()->json(['error' => 'Too many requests'], 429);
        }
        
        // Get active keypair
        $keypair = UserKeypair::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        if (!$keypair) {
            return response()->json([
                'error' => 'No active keypair found',
                'needs_initialization' => true
            ], 404);
        }
        
        // Check rotation status
        $status = KeyRotationService::checkKeyRotationStatus(
            $keypair->key_id,
            [
                'created_at' => $keypair->created_at,
                'algorithm' => $keypair->algorithm ?? 'ML-KEM-512'
            ]
        );
        
        // Add user-friendly recommendations
        $recommendations = [];
        if ($status['urgency'] === 'critical') {
            $recommendations[] = 'Immediate key rotation required for security';
        } elseif ($status['urgency'] === 'urgent') {
            $recommendations[] = 'Key rotation recommended within 24 hours';
        } elseif ($status['urgency'] === 'recommended') {
            $recommendations[] = 'Consider rotating keys soon for optimal security';
        }
        
        return response()->json([
            'status' => $status,
            'recommendations' => $recommendations,
            'current_key_id' => substr($keypair->key_id, 0, 8) . '...',
            'key_age_days' => $keypair->created_at->diffInDays(now()),
            'rotation_available' => true
        ]);
    }
    
    /**
     * Initiate key rotation
     * POST /api/keys/rotate
     */
    public function rotateKeys(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Rate limit rotation attempts
        $key = 'rotation-perform:' . $user->id;
        if (!RateLimiter::attempt($key, 1, function() {
            // Callback required by RateLimiter
        }, 300)) { // 1 rotation per 5 minutes
            return response()->json(['error' => 'Key rotation already in progress'], 429);
        }
        
        // Validate secure random for new key generation
        $randomTest = SecureRandomValidator::generateSecureRandom(32);
        $validation = SecureRandomValidator::validateRandomBytes($randomTest);
        
        if (!$validation['valid']) {
            return response()->json([
                'error' => 'Cannot rotate keys - random number generator failed validation',
                'details' => $validation['issues']
            ], 500);
        }
        
        // Get current keypair
        $currentKeypair = UserKeypair::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        $oldKeyId = $currentKeypair ? $currentKeypair->key_id : null;
        
        // Perform rotation
        $result = KeyRotationService::rotateUserKeys($user, $oldKeyId);
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'] ?? 'Key rotation failed'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Keys rotated successfully',
            'new_key_id' => substr($result['new_key_id'], 0, 8) . '...',
            'old_key_id' => $oldKeyId ? substr($oldKeyId, 0, 8) . '...' : null,
            'grace_period_ends' => $result['grace_period_ends'] ?? null,
            'action_required' => 'Update any stored references to use the new key'
        ]);
    }
    
    /**
     * Force emergency key rotation
     * POST /api/keys/emergency-rotate
     */
    public function emergencyRotation(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'reason' => 'required|string|max:500',
            'confirm' => 'required|boolean|accepted'
        ]);
        
        // Rate limit emergency rotations more strictly
        $key = 'emergency-rotation:' . $user->id;
        if (!RateLimiter::attempt($key, 1, function() {
            // Callback required by RateLimiter
        }, 3600)) { // 1 per hour
            return response()->json(['error' => 'Emergency rotation already performed recently'], 429);
        }
        
        // Perform emergency rotation
        $result = KeyRotationService::forceRotation($user, $request->input('reason'));
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['error'] ?? 'Emergency rotation failed'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Emergency key rotation completed',
            'new_key_id' => substr($result['new_key_id'], 0, 8) . '...',
            'warning' => 'All content encrypted with old key will be inaccessible until re-encrypted'
        ]);
    }
    
    /**
     * Get key rotation history
     * GET /api/keys/rotation-history
     */
    public function rotationHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $history = UserKeypair::where('user_id', $user->id)
            ->whereNotNull('rotation_from')
            ->orWhereNotNull('rotation_to')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($keypair) {
                return [
                    'key_id' => substr($keypair->key_id, 0, 8) . '...',
                    'rotated_from' => $keypair->rotation_from ? substr($keypair->rotation_from, 0, 8) . '...' : null,
                    'rotated_to' => $keypair->rotation_to ? substr($keypair->rotation_to, 0, 8) . '...' : null,
                    'rotation_date' => $keypair->rotation_initiated_at ?? $keypair->created_at,
                    'status' => $keypair->rotation_status ?? 'completed',
                    'expires_at' => $keypair->expires_at
                ];
            });
        
        return response()->json([
            'history' => $history,
            'total_rotations' => $history->count()
        ]);
    }
    
    /**
     * Get system-wide rotation statistics (admin only)
     * GET /api/admin/rotation-stats
     */
    public function getRotationStats(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $stats = KeyRotationService::getRotationStats();
        
        return response()->json($stats);
    }
}
