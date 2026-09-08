<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_accepts_a_valid_email_and_password(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => ' USER@EXAMPLE.COM ',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.email', 'user@example.com')
            ->assertJsonStructure(['access_token', 'token_type', 'user']);
    }

    public function test_login_returns_clear_validation_messages_for_invalid_fields(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password'])
            ->assertJsonPath(
                'errors.email.0',
                'Please enter a valid email address.'
            )
            ->assertJsonPath(
                'errors.password.0',
                'The password must be at least 8 characters.'
            );
    }

    public function test_login_requires_both_fields(): void
    {
        $response = $this->postJson('/api/login');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password'])
            ->assertJsonPath(
                'errors.email.0',
                'Please enter your email address.'
            )
            ->assertJsonPath(
                'errors.password.0',
                'Please enter your password.'
            );
    }

    public function test_login_rejects_invalid_credentials_and_inactive_accounts(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $invalidCredentials = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpass',
        ]);

        $invalidCredentials
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                'The email address or password is incorrect.'
            );

        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $inactive = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $inactive
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                'This account is inactive. Please contact an administrator.'
            );
    }
}
