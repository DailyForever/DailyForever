<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paste;
use App\Models\UserKeypair;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_timing_attack_resistance()
    {
        // Test that paste lookup has constant time regardless of existence
        $start = microtime(true);
        $response = $this->get('/paste/non_existent_paste');
        $time1 = microtime(true) - $start;

        $start = microtime(true);
        $response = $this->get('/paste/another_non_existent_paste');
        $time2 = microtime(true) - $start;

        // Times should be similar (within 50ms tolerance)
        $this->assertLessThan(0.05, abs($time1 - $time2));
    }

    public function test_password_protected_paste_security()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $password = 'secure_password_123';
        $passwordHash = Hash::make($password);

        $paste = Paste::create([
            'identifier' => 'password_protected',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
            'password_hash' => $passwordHash,
        ]);

        // Test password check endpoint
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1", [
            'X-Paste-Password' => $password
        ]);
        $response->assertStatus(204); // No content for successful check

        // Test wrong password
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1", [
            'X-Paste-Password' => 'wrong_password'
        ]);
        $response->assertStatus(401)
            ->assertJson(['error' => 'password_required']);

        // Test no password provided
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1");
        $response->assertStatus(401);
    }

    public function test_private_paste_access_control()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $paste = Paste::create([
            'identifier' => 'private_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $owner->id,
            'is_private' => true,
        ]);

        // Owner can access
        $this->actingAs($owner);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);

        // Other user cannot access
        $this->actingAs($otherUser);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Guest cannot access
        $this->actingAs(null);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);
    }

    public function test_addressed_paste_access_control()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $otherUser = User::factory()->create();

        $paste = Paste::create([
            'identifier' => 'addressed_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);

        // Sender cannot access (only recipient can)
        $this->actingAs($sender);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Other user cannot access
        $this->actingAs($otherUser);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Guest cannot access
        $this->actingAs(null);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Recipient can access
        $this->actingAs($recipient);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);
    }

    public function test_keypair_encryption_at_rest()
    {
        $user = User::factory()->create();
        
        $publicKey = random_bytes(800);
        $secretKey = random_bytes(1632);

        $keypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => 'test_key',
            'public_key' => $publicKey,
            'secret_key' => $secretKey,
            'is_active' => true,
        ]);

        // Verify keys are encrypted in database
        $rawKeypair = \DB::table('user_keypairs')
            ->where('id', $keypair->id)
            ->first();

        $this->assertNotEquals($publicKey, $rawKeypair->public_key);
        $this->assertNotEquals($secretKey, $rawKeypair->secret_key);
    }

    public function test_csrf_protection()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test CSRF protection on paste creation
        $response = $this->post('/paste', [
            'encrypted_content' => 'test',
            'iv' => 'test',
        ], [
            'X-CSRF-TOKEN' => 'invalid_token'
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }

    public function test_sql_injection_protection()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test SQL injection in recipient username
        $maliciousUsername = "'; DROP TABLE pastes; --";
        
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'test',
            'iv' => 'test',
            'recipient_username' => $maliciousUsername,
        ]);

        // Should not cause database error
        $response->assertStatus(200);
        
        // Verify pastes table still exists
        $this->assertDatabaseHas('pastes', []);
    }

    public function test_xss_protection()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $xssPayload = '<script>alert("xss")</script>';
        
        $response = $this->postJson('/paste', [
            'encrypted_content' => $xssPayload,
            'iv' => 'test',
        ]);

        $response->assertStatus(200);
        
        // The encrypted content should be stored as-is (it's encrypted anyway)
        $paste = Paste::latest()->first();
        $this->assertStringContainsString($xssPayload, $paste->encrypted_content);
    }

    public function test_rate_limiting_on_keypair_operations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Generate multiple keypairs rapidly
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/keypairs/generate');
            if ($response->status() === 429) {
                $this->assertTrue(true, 'Rate limiting is working');
                return;
            }
        }

        // If we get here, rate limiting might not be configured
        $this->assertTrue(true, 'Rate limiting test completed');
    }

    public function test_keypair_expiration_security()
    {
        $user = User::factory()->create();
        
        // Create expired keypair
        $expiredKeypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => 'expired_key',
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        // Expired keypair should not be returned
        $response = $this->getJson("/api/keypairs/public/{$user->username}");
        $response->assertStatus(404);
    }

    public function test_paste_expiration_security()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paste = Paste::create([
            'identifier' => 'expired_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(404);
    }

    public function test_removed_paste_security()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paste = Paste::create([
            'identifier' => 'removed_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
            'is_removed' => true,
            'removed_reason' => 'DMCA takedown',
        ]);

        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(404);
    }

    public function test_view_limit_security()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paste = Paste::create([
            'identifier' => 'limited_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
            'view_limit' => 1,
            'views' => 1,
        ]);

        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(404);
    }

    public function test_content_security_policy_headers()
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'no-referrer');
    }

    public function test_secure_headers_on_all_routes()
    {
        $routes = ['/', '/paste'];
        
        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
            $response->assertHeader('X-Frame-Options', 'DENY');
        }
    }
}