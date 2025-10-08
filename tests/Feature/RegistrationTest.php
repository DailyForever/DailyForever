<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_displays_correctly()
    {
        $response = $this->get(route('auth.register.show'));

        $response->assertStatus(200);
        $response->assertSee('Create Your Account');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('PIN (4-8 digits)');
        $response->assertSee('Create Account');
    }

    public function test_user_can_register_with_valid_data()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertRedirect(route('paste.create'));
        $response->assertSessionHas('success', 'Account created successfully! Welcome to DailyForever.');

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'name' => 'testuser',
        ]);

        $user = User::where('username', 'testuser')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue(Hash::check('1234', $user->pin_hash));
    }

    public function test_registration_fails_with_duplicate_username()
    {
        User::factory()->create(['username' => 'existinguser']);

        $userData = [
            'username' => 'existinguser',
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_fails_with_invalid_username()
    {
        $userData = [
            'username' => 'user@invalid', // Contains invalid character
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_short_username()
    {
        $userData = [
            'username' => 'ab', // Too short
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_long_username()
    {
        $userData = [
            'username' => str_repeat('a', 65), // Too long
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_short_password()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'short', // Too short
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_invalid_pin()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'pin' => '12', // Too short
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['pin']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_long_pin()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'pin' => '123456789', // Too long
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['pin']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_non_numeric_pin()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'pin' => 'abcd', // Non-numeric
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['pin']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_fails_with_missing_fields()
    {
        $response = $this->post(route('auth.register'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username', 'password', 'pin']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_user_is_automatically_logged_in_after_registration()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'pin' => '1234',
        ];

        $response = $this->post(route('auth.register'), $userData);

        $response->assertStatus(302);
        $this->assertAuthenticated();
        
        $user = auth()->user();
        $this->assertEquals('testuser', $user->username);
    }

    public function test_registration_with_valid_username_characters()
    {
        $validUsernames = ['user123', 'test_user', 'user-name', 'User123', 'a1b2c3'];

        foreach ($validUsernames as $username) {
            $userData = [
                'username' => $username,
                'password' => 'password123',
                'pin' => '1234',
            ];

            $response = $this->post(route('auth.register'), $userData);
            $response->assertStatus(302);
            $response->assertSessionHasNoErrors();
            
            $this->assertDatabaseHas('users', ['username' => $username]);
            
            // Clean up for next iteration
            User::where('username', $username)->delete();
        }
    }

    public function test_registration_with_edge_case_pin_lengths()
    {
        $validPins = ['1234', '12345', '123456', '1234567', '12345678'];

        foreach ($validPins as $pin) {
            $userData = [
                'username' => 'testuser' . $pin,
                'password' => 'password123',
                'pin' => $pin,
            ];

            $response = $this->post(route('auth.register'), $userData);
            $response->assertStatus(302);
            $response->assertSessionHasNoErrors();
            
            $this->assertDatabaseHas('users', ['username' => 'testuser' . $pin]);
            
            // Clean up for next iteration
            User::where('username', 'testuser' . $pin)->delete();
        }
    }
}
