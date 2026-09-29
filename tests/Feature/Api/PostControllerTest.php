<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
 
    private User $user;
 
    protected function setUp(): void
    {
        parent::setUp();
 
        $this->user = User::factory()->create();
    }
 
    /**
     * Authenticate the request as the test user via Sanctum.
     */
    private function api(): static
    {
        return $this->actingAs($this->user, 'sanctum');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'user_id' => $this->user->id,
            'title' => 'My First Post',
            'intro' => 'A short introduction.',
            'description' => 'The full description of the post.',
            'is_active' => 1,
        ], $overrides);
    }

    #[Test]
    public function detail_returns_the_post_with_user(): void
    {
        $post = Post::factory()->for($this->user)->create();
 
        $this->api()
            ->getJson("/api/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.title', $post->title)
            ->assertJsonPath('data.user.id', $this->user->id);
    }

    #[Test]
    public function create_stores_a_new_post(): void
    {
        $payload = $this->validPayload();
 
        $this->api()
            ->postJson('/api/posts', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.user_id', $this->user->id)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
 
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $payload);
    }
}
