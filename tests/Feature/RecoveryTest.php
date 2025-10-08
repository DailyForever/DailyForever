<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_generate_recovery_token()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('token');
        $response->assertSessionHas('recovery_username', 'testuser');

        $user->refresh();
        $this->assertNotNull($user->recovery_token);
        $this->assertNotNull($user->recovery_token_expires_at);
        $this->assertTrue(Carbon::now()->addMinutes(15)->diffInMinutes($user->recovery_token_expires_at) < 1);
    }

    public function test_recovery_token_format_is_user_friendly()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);

        $token = session('token');
        $this->assertNotNull($token);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $token);
    }

    public function test_user_can_complete_recovery_with_valid_token()
    {
        $user = User::factory()->create(['username' => 'testuser']);
        $oldPinHash = $user->pin_hash;

        // Generate recovery token
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);
        $token = session('token');

        // Complete recovery
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => '123456',
            'confirm_pin' => '123456'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login.show'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotEquals($oldPinHash, $user->pin_hash);
        $this->assertTrue(Hash::check('123456', $user->pin_hash));
        $this->assertNull($user->recovery_token);
        $this->assertNull($user->recovery_token_expires_at);
    }

    public function test_recovery_fails_with_invalid_token()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => 'INVALID-TOKEN-1234-5678',
            'new_pin' => '123456',
            'confirm_pin' => '123456'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['token']);
    }

    public function test_recovery_fails_with_expired_token()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'recovery_token' => 'ABCD-EFGH-IJKL-MNOP',
            'recovery_token_expires_at' => Carbon::now()->subMinutes(1)
        ]);

        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => 'ABCD-EFGH-IJKL-MNOP',
            'new_pin' => '123456',
            'confirm_pin' => '123456'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['token']);
    }

    public function test_recovery_fails_with_mismatched_pin_confirmation()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        // Generate recovery token
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);
        $token = session('token');

        // Try to complete recovery with mismatched PINs
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => '123456',
            'confirm_pin' => '654321'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['confirm_pin']);
    }

    public function test_recovery_fails_with_invalid_pin_length()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        // Generate recovery token
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);
        $token = session('token');

        // Try to complete recovery with invalid PIN
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => '123', // Too short
            'confirm_pin' => '123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['new_pin']);
    }

    public function test_recovery_fails_with_non_numeric_pin()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        // Generate recovery token
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);
        $token = session('token');

        // Try to complete recovery with non-numeric PIN
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => 'abc123',
            'confirm_pin' => 'abc123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['new_pin']);
    }

    public function test_recovery_page_displays_correctly()
    {
        $response = $this->get(route('auth.recover.show'));

        $response->assertStatus(200);
        $response->assertSee('Recover Your Account');
        $response->assertSee('Step 1: Get Recovery Token');
        $response->assertSee('Username');
    }

    public function test_recovery_token_can_only_be_used_once()
    {
        $user = User::factory()->create(['username' => 'testuser']);

        // Generate recovery token
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'testuser'
        ]);
        $token = session('token');

        // Complete recovery first time
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => '123456',
            'confirm_pin' => '123456'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login.show'));

        // Try to use the same token again
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'testuser',
            'token' => $token,
            'new_pin' => '789012',
            'confirm_pin' => '789012'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['token']);
    }

    public function test_recovery_works_with_different_users()
    {
        $user1 = User::factory()->create(['username' => 'user1']);
        $user2 = User::factory()->create(['username' => 'user2']);

        // Generate token for user1
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'user1'
        ]);
        $token1 = session('token');

        // Generate token for user2
        $response = $this->post(route('auth.recover.start'), [
            'username' => 'user2'
        ]);
        $token2 = session('token');

        // Complete recovery for user1
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'user1',
            'token' => $token1,
            'new_pin' => '111111',
            'confirm_pin' => '111111'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login.show'));

        // Complete recovery for user2
        $response = $this->post(route('auth.recover.complete'), [
            'username' => 'user2',
            'token' => $token2,
            'new_pin' => '222222',
            'confirm_pin' => '222222'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login.show'));

        // Verify both users have their new PINs
        $user1->refresh();
        $user2->refresh();
        $this->assertTrue(Hash::check('111111', $user1->pin_hash));
        $this->assertTrue(Hash::check('222222', $user2->pin_hash));
    }
}