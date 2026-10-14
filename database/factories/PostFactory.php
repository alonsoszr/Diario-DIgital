<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'title' => fake()->unique()->sentence(6),
            'excerpt' => fake()->sentence(14),
            'body' => implode("\n\n", fake()->paragraphs(3)),
            'status' => Post::PUBLICADO,
            'published_at' => now()->subDay(),
            'views' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => Post::BORRADOR, 'published_at' => null]);
    }
}
