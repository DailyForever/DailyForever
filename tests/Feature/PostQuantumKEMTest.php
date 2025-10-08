<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserKeypair;
use App\Models\Paste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class PostQuantumKEMTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_generate_keypair()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/keypairs/generate', [
            'expires_in_days' => 30
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'key_id',
                'expires_at'
            ]);

        $this->assertDatabaseHas('user_keypairs', [
            'user_id' => $user->id,
            'is_active' => true
        ]);
    }

    public function test_user_can_update_keypair_with_keys()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Generate keypair first
        $generateResponse = $this->postJson('/api/keypairs/generate');
        $keyId = $generateResponse->json('key_id');

        $publicKey = base64_encode(random_bytes(800)); // Mock Kyber public key
        $secretKey = base64_encode(random_bytes(1632)); // Mock Kyber secret key

        $response = $this->putJson('/api/keypairs/update', [
            'key_id' => $keyId,
            'public_key' => $publicKey,
            'secret_key' => $secretKey
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $keypair = UserKeypair::where('key_id', $keyId)->first();
        $this->assertNotNull($keypair->public_key);
        $this->assertNotNull($keypair->secret_key);
    }

    public function test_public_key_lookup_works()
    {
        $user = User::factory()->create(['username' => 'testuser']);
        
        // Create a keypair for the user
        $keypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => 'test_key_123',
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/keypairs/public/testuser');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'key_id',
                'public_key',
                'expires_at'
            ]);
    }

    public function test_addressed_paste_creation_works()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create(['username' => 'recipient']);
        
        $this->actingAs($sender);

        // Create keypair for recipient
        $recipientKeypair = UserKeypair::create([
            'user_id' => $recipient->id,
            'key_id' => 'recipient_key_123',
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'is_active' => true,
        ]);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'expires_in' => '1day',
            'recipient_username' => 'recipient',
            'kem_alg' => 'kyber-512',
            'kem_kid' => 'recipient_key_123',
            'kem_ct' => base64_encode(random_bytes(800)),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'url']);

        $this->assertDatabaseHas('pastes', [
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'kem_alg' => 'kyber-512',
            'kem_kid' => 'recipient_key_123'
        ]);
    }

    public function test_addressed_paste_access_control()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $this->actingAs($sender);

        // Create addressed paste
        $paste = Paste::create([
            'identifier' => 'test_paste_123',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'kem_alg' => 'kyber-512',
            'kem_kid' => 'test_key',
            'kem_ct' => random_bytes(800),
        ]);

        // Sender should not be able to view (only recipient can)
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Other user should not be able to view
        $this->actingAs($otherUser);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(403);

        // Recipient should be able to view
        $this->actingAs($recipient);
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);
    }

    public function test_keypair_expiration_handling()
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

        // Create active keypair
        $activeKeypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => 'active_key',
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'is_active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->getJson("/api/keypairs/public/{$user->username}");
        
        $response->assertStatus(200);
        $this->assertEquals('active_key', $response->json('key_id'));
    }

    public function test_keypair_revocation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create keypair
        $keypair = UserKeypair::create([
            'user_id' => $user->id,
            'key_id' => 'test_key',
            'public_key' => random_bytes(800),
            'secret_key' => random_bytes(1632),
            'is_active' => true,
        ]);

        // Revoke keypair
        $response = $this->deleteJson('/api/keypairs/revoke', [
            'key_id' => 'test_key'
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $keypair->refresh();
        $this->assertFalse($keypair->is_active);
    }

    public function test_unauthorized_access_to_keypair_operations()
    {
        // Test without authentication
        $response = $this->postJson('/api/keypairs/generate');
        $response->assertStatus(401);

        $response = $this->putJson('/api/keypairs/update', [
            'key_id' => 'test',
            'public_key' => 'test',
            'secret_key' => 'test'
        ]);
        $response->assertStatus(401);

        $response = $this->getJson('/api/keypairs');
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/keypairs/revoke', [
            'key_id' => 'test'
        ]);
        $response->assertStatus(401);
    }

    public function test_invalid_keypair_operations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test updating non-existent keypair
        $response = $this->putJson('/api/keypairs/update', [
            'key_id' => 'non_existent',
            'public_key' => base64_encode(random_bytes(800)),
            'secret_key' => base64_encode(random_bytes(1632))
        ]);
        $response->assertStatus(404);

        // Test revoking non-existent keypair
        $response = $this->deleteJson('/api/keypairs/revoke', [
            'key_id' => 'non_existent'
        ]);
        $response->assertStatus(404);
    }

    public function test_view_limit_enforcement_with_addressed_pastes()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $this->actingAs($sender);

        // Create addressed paste with view limit
        $paste = Paste::create([
            'identifier' => 'limited_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'view_limit' => 1,
            'views' => 0,
        ]);

        $this->actingAs($recipient);

        // First view should work
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);

        // Check that paste was deleted after first view
        $this->assertDatabaseMissing('pastes', ['identifier' => $paste->identifier]);

        // Second view should fail (paste deleted)
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(404);
    }
}