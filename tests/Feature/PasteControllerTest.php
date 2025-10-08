<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PasteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_paste()
    {
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'expires_in' => '1day',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'url']);

        $this->assertDatabaseHas('pastes', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
        ]);
    }

    public function test_authenticated_user_can_create_private_paste()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'is_private' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pastes', [
            'user_id' => $user->id,
            'is_private' => true,
        ]);
    }

    public function test_paste_expiration_handling()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test 1 hour expiration
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'expires_in' => '1hour',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $expectedExpiry = Carbon::now()->addHour();
        $this->assertTrue($paste->expires_at->diffInMinutes($expectedExpiry) < 1);

        // Test 1 day expiration
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data_2',
            'iv' => 'iv_data_2',
            'expires_in' => '1day',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $expectedExpiry = Carbon::now()->addDay();
        $this->assertTrue($paste->expires_at->diffInMinutes($expectedExpiry) < 1);

        // Test 1 week expiration
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data_3',
            'iv' => 'iv_data_3',
            'expires_in' => '1week',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $expectedExpiry = Carbon::now()->addWeek();
        $this->assertTrue($paste->expires_at->diffInMinutes($expectedExpiry) < 1);

        // Test 1 month expiration
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data_4',
            'iv' => 'iv_data_4',
            'expires_in' => '1month',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $expectedExpiry = Carbon::now()->addMonth();
        $this->assertTrue($paste->expires_at->diffInMinutes($expectedExpiry) < 1);

        // Test never expires
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data_5',
            'iv' => 'iv_data_5',
            'expires_in' => 'never',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertNull($paste->expires_at);
    }

    public function test_view_limit_functionality()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'view_limit' => 2,
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertEquals(2, $paste->view_limit);

        // First view
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);
        $paste->refresh();
        $this->assertEquals(1, $paste->views);

        // Second view
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);
        $paste->refresh();
        $this->assertEquals(2, $paste->views);

        // Third view should fail (paste deleted)
        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(404);
    }

    public function test_password_protected_paste()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'password' => 'secure_password',
            'password_hint' => 'My favorite color',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertNotNull($paste->password_hash);
        $this->assertEquals('My favorite color', $paste->password_hint);
        $this->assertTrue(password_verify('secure_password', $paste->password_hash));
    }

    public function test_password_verification_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a password-protected paste
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'password' => 'test_password',
            'password_hint' => 'Test hint',
        ]);

        $response->assertStatus(200);
        $paste = Paste::latest()->first();

        // Test correct password
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1", [
            'X-Paste-Password' => 'test_password'
        ]);
        $response->assertStatus(204);

        // Test incorrect password
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1", [
            'X-Paste-Password' => 'wrong_password'
        ]);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'password_required', 'hint' => 'Test hint']);

        // Test no password provided
        $response = $this->get("/paste/{$paste->identifier}?pw_check=1");
        $response->assertStatus(401);
    }

    public function test_paste_validation()
    {
        // Test missing required fields
        $response = $this->postJson('/paste', []);
        $response->assertStatus(422);

        // Test invalid expiration
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'expires_in' => 'invalid',
        ]);
        $response->assertStatus(422);

        // Test invalid view limit
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'view_limit' => -1,
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'view_limit' => 1000001,
        ]);
        $response->assertStatus(422);
    }

    public function test_paste_creation_with_recipient()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create(['username' => 'recipient']);
        
        $this->actingAs($sender);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'recipient_username' => 'recipient',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertEquals($recipient->id, $paste->recipient_id);
    }

    public function test_paste_creation_with_invalid_recipient()
    {
        $sender = User::factory()->create();
        $this->actingAs($sender);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'recipient_username' => 'non_existent_user',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertNull($paste->recipient_id);
    }

    public function test_paste_show_functionality()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paste = Paste::create([
            'identifier' => 'test_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
        ]);

        $response = $this->get("/paste/{$paste->identifier}");
        $response->assertStatus(200);

        $paste->refresh();
        $this->assertEquals(1, $paste->views);
    }

    public function test_paste_not_found()
    {
        $response = $this->get('/paste/non_existent_paste');
        $response->assertStatus(404);
    }

    public function test_paste_raw_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $paste = Paste::create([
            'identifier' => 'test_paste',
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/paste/{$paste->identifier}/raw");
        $response->assertStatus(200)
            ->assertJson([
                'encrypted_content' => 'encrypted_data',
                'iv' => 'iv_data',
            ]);
    }

    public function test_paste_raw_endpoint_not_found()
    {
        $response = $this->getJson('/paste/non_existent/raw');
        $response->assertStatus(404)
            ->assertJson(['error' => 'Paste not found or expired']);
    }

    public function test_paste_creation_timestamp_jitter()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $createdAt = $paste->created_at;
        
        // Should be within the current hour (jitter applied)
        $this->assertTrue($createdAt->isCurrentHour());
        
        // Should have some jitter (not exactly on the hour)
        $this->assertNotEquals(0, $createdAt->minute);
    }

    public function test_guest_cannot_create_private_paste()
    {
        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'is_private' => true,
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertFalse($paste->is_private);
    }

    public function test_paste_creation_with_kem_metadata()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/paste', [
            'encrypted_content' => 'encrypted_data',
            'iv' => 'iv_data',
            'kem_alg' => 'kyber-512',
            'kem_kid' => 'test_key_123',
            'kem_ct' => base64_encode(random_bytes(800)),
        ]);

        $response->assertStatus(200);

        $paste = Paste::latest()->first();
        $this->assertEquals('kyber-512', $paste->kem_alg);
        $this->assertEquals('test_key_123', $paste->kem_kid);
        $this->assertNotNull($paste->kem_ct);
    }
}