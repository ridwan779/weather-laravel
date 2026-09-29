<?php

namespace Tests\Feature\Api;

use App\Helpers\Weather;
use App\Models\User;

use Mockery\MockInterface;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;
 
    private User $user;

    private array $weatherData = [
        'location' => ['name' => 'Perth', 'country' => 'Australia'],
        'current' => ['temp_c' => 22.5, 'condition' => ['text' => 'Sunny']],
    ];

    protected function setUp(): void
    {
        parent::setUp();
 
        Cache::flush();

        $this->user = User::factory()->create();
    }

    private function api(): static
    {
        return $this->actingAs($this->user, 'sanctum');
    }

    #[Test]
    public function get_data_from_api(): void
    {
        $this->mock(Weather::class, function (MockInterface $mock) {
            $mock->shouldReceive('current')
                ->once()
                ->with('Perth')
                ->andReturn(['status' => true, 'data' => $this->weatherData]);
        });
 
        $response = $this->api()->getJson('/api/weather');
 
        $response->assertOk()
            ->assertExactJson($this->weatherData);
    }
}
