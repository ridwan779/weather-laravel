<?php
namespace Database\Factories;
 
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
 
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'intro' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'is_active' => fake()->randomElement([0, 1]),
        ];
    }
}