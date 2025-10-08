<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserKeypair;
use App\Services\KeyRotationService;
use App\Services\SecureRandomValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class KeyRotationTest extends TestCase
{
    use RefreshDatabase;

    public function test_key_rotation_check_detects_old_keys()
    {
        $keyId = bin2hex(random_bytes(16));
        $metadata = [
            'created_at' => now()->subDays(100), // Older than 90 days
            'algorithm' => 'ML-KEM-512'
        ];
        
        $status = KeyRotationService::checkKeyRotationStatus($keyId, $metadata);
        
        $this->assertTrue($status['needs_rotation']);
        $this->assertEquals('critical', $status['urgency']);
        $this->assertStringContainsString('Key age exceeds 90 days', $status['reasons'][0]);
    }
    
    public function test_key_rotation_check_detects_high_usage()
    {
        $keyId = bin2hex(random_bytes(16));
        Cache::put("key_usage:{$keyId}", 11000); // Above 10000 limit
        
        $metadata = [
            'created_at' => now(),
            'algorithm' => 'ML-KEM-512'
        ];
        
        $status = KeyRotationService::checkKeyRotationStatus($keyId, $metadata);
        
        $this->assertTrue($status['needs_rotation']);
        $this->assertEquals('critical', $status['urgency']);
        $this->assertStringContainsString('usage exceeds', $status['reasons'][0]);
    }
    
    public function test_key_rotation_creates_new_keypair()
    {
        $user = User::factory()->create();
        $oldKeyId = bin2hex(random_bytes(16));
        
        // Create old keypair
        UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => $oldKeyId,
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'algorithm' => 'ML-KEM-512',
            'is_active' => true
        ]);
        
        $result = KeyRotationService::rotateUserKeys($user, $oldKeyId);
        
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['new_key_id']);
        $this->assertNotEquals($oldKeyId, $result['new_key_id']);
        
        // Verify new keypair exists
        $newKeypair = UserKeypair::where('key_id', $result['new_key_id'])->first();
        $this->assertNotNull($newKeypair);
        $this->assertTrue($newKeypair->is_active);
        $this->assertEquals($oldKeyId, $newKeypair->rotation_from);
        
        // Verify old keypair is marked for rotation
        $oldKeypair = UserKeypair::where('key_id', $oldKeyId)->first();
        $this->assertEquals($result['new_key_id'], $oldKeypair->rotation_to);
        $this->assertNotNull($oldKeypair->rotation_initiated_at);
    }
    
    public function test_secure_random_validation_detects_low_quality()
    {
        // Test all zeros (catastrophic failure)
        $badRandom = str_repeat("\x00", 16);
        $validation = SecureRandomValidator::validateRandomBytes($badRandom);
        
        $this->assertFalse($validation['valid']);
        $this->assertEquals(0, $validation['score']);
        $this->assertTrue($validation['critical'] ?? false);
        $this->assertContains('CRITICAL: All bytes have the same value', $validation['issues']);
    }
    
    public function test_secure_random_validation_accepts_good_random()
    {
        // Use actual random bytes
        $goodRandom = random_bytes(32);
        $validation = SecureRandomValidator::validateRandomBytes($goodRandom);
        
        // Good random should pass most checks
        $this->assertTrue($validation['valid']);
        $this->assertGreaterThan(0.7, $validation['score']);
        $this->assertEmpty($validation['issues']);
    }
    
    public function test_secure_random_generation_with_validation()
    {
        $random = SecureRandomValidator::generateSecureRandom(32, ['validate' => true]);
        
        $this->assertEquals(32, strlen($random));
        
        // Validate the generated random
        $validation = SecureRandomValidator::validateRandomBytes($random);
        $this->assertTrue($validation['valid']);
    }
    
    public function test_emergency_rotation_clears_compromised_key()
    {
        $user = User::factory()->create();
        
        // Create active keypair
        $keypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => bin2hex(random_bytes(16)),
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'algorithm' => 'ML-KEM-512',
            'is_active' => true
        ]);
        
        $result = KeyRotationService::forceRotation($user, 'Suspected compromise');
        
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['new_key_id']);
        
        // Check compromise flag
        $compromiseFlag = Cache::get("key_compromised:{$keypair->key_id}");
        $this->assertNotNull($compromiseFlag);
        $this->assertEquals('Suspected compromise', $compromiseFlag['reason']);
    }
    
    public function test_key_rotation_api_endpoint_requires_auth()
    {
        $response = $this->getJson('/api/keys/rotation-status');
        $response->assertStatus(401);
        
        $response = $this->postJson('/api/keys/rotate');
        $response->assertStatus(401);
    }
    
    public function test_key_rotation_api_with_valid_user()
    {
        $user = User::factory()->create();
        
        // Create active keypair with old timestamp
        $keypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => bin2hex(random_bytes(16)),
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'algorithm' => 'ML-KEM-512',
            'is_active' => true
        ]);
        
        // Force update the created_at timestamp
        $keypair->created_at = now()->subDays(100);
        $keypair->save();
        
        $this->actingAs($user);
        
        // Check status
        $response = $this->getJson('/api/keys/rotation-status');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status' => ['needs_rotation', 'urgency', 'reasons'],
                'recommendations',
                'current_key_id',
                'key_age_days'
            ]);
        
        $data = $response->json();
        
        // The key age should be 100 days, which is > 90 days
        $this->assertGreaterThanOrEqual(100, $data['key_age_days']);
        $this->assertTrue($data['status']['needs_rotation']);
        $this->assertEquals('critical', $data['status']['urgency']);
    }
    
    public function test_pattern_detection_in_random_validation()
    {
        // Sequential pattern
        $sequential = "";
        for ($i = 0; $i < 16; $i++) {
            $sequential .= chr($i);
        }
        
        $validation = SecureRandomValidator::validateRandomBytes($sequential);
        $this->assertFalse($validation['valid']);
        // Check for either 'Sequential pattern' or 'increasing sequence'
        $issuesString = implode(', ', $validation['issues']);
        $this->assertTrue(
            str_contains($issuesString, 'Sequential pattern') || 
            str_contains($issuesString, 'increasing sequence'),
            "Expected pattern detection in: $issuesString"
        );
    }
    
    public function test_rotation_stats_for_admin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        
        $response = $this->getJson('/api/admin/rotation-stats');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'recent_rotations_count',
                'pending_rotations_count',
                'policies'
            ]);
    }
}
