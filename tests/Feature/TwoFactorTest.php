<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_shows(): void
    {
        $user = User::factory()->create([
            'username' => 'alice',
            'password' => Hash::make('password123'),
        ]);
        $resp = $this->actingAs($user)->get(route('settings.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Two‑Factor Authentication');
    }

    public function test_otpauth_endpoint_returns_secret(): void
    {
        $user = User::factory()->create([
            'username' => 'bob',
            'password' => Hash::make('password123'),
        ]);
        $resp = $this->actingAs($user)->get(route('settings.2fa.otpauth'));
        $resp->assertStatus(200);
        $resp->assertJsonStructure(['secret','issuer','account','uri','image']);
    }

    public function test_2fa_challenge_page_renders(): void
    {
        $user = User::factory()->create([
            'username' => 'carol',
            'password' => Hash::make('password123'),
            'two_factor_enabled' => true,
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP', // known base32
        ]);
        session(['2fa:user_id' => $user->id]);
        $resp = $this->get(route('auth.2fa.show'));
        $resp->assertStatus(200);
        $resp->assertSee('Two‑Factor Authentication');
    }
}


