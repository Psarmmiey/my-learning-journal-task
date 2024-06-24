<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $isPublished = $this->faker->boolean();

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'title' => $this->faker->sentence(5),
            'excerpt' => $this->faker->paragraph(2),
            'body' => $this->faker->text(1000),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? Carbon::now() : null,
            'user_id' => User::factory(),
        ];
    }
}
