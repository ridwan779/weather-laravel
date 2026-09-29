<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent SendEmail from actually running during tests
        Queue::fake();
    }

    /**
     * A valid registration payload, with optional overrides.
     */
    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'secret123',
        ], $overrides);
    }

    // -----------------------------------------------------------------
    // POST /api/register
    // -----------------------------------------------------------------

    #[Test]
    public function register_creates_a_user_and_returns_a_token(): void
    {
        $this->postJson('/api/register', $this->registerPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Budi Santoso')
            ->assertJsonPath('data.email', 'budi@example.com')
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at'], 'token'])
            ->assertJsonMissingPath('data.password');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    // -----------------------------------------------------------------
    // POST /api/login
    // -----------------------------------------------------------------

    #[Test]
    public function login_returns_the_user_and_a_token_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'budi@example.com',
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonStructure(['data' => ['id', 'name', 'email'], 'token'])
            ->assertJsonMissingPath('data.password');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    #[Test]
    public function login_fails_with_a_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'budi@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'budi@example.com',
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'Email or password is wrong'])
            ->assertJsonMissingPath('token');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function login_fails_when_the_email_is_not_registered(): void
    {
        $this->postJson('/api/login', [
            'email' => 'nobody@example.com',
            'password' => 'secret123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

}