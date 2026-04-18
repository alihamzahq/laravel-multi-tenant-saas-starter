<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Central;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'correct-password',
            'is_admin' => true,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_rejects_non_admin_users(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_returns_token_for_valid_admin_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
            ],
        ]);
    }
}
