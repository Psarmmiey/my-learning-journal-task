<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use UnexpectedValueException;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $users = \App\Models\User::all();

        if ($users->count() === 0) {
            throw new UnexpectedValueException('No user record found');
        }

        foreach ($users as $user) {
            $factory = \App\Models\BlogPost::factory();

            $posts = $factory
                ->count(10)
                ->create([
                    'user_id' => $user->getKey(),
                ]);

            foreach ($posts as $post) {
                $post->copyMedia(resource_path('images/placeholder-500x350.png'))
                    ->toMediaCollection('images');
            }
        }

        $user = User::first();
        $firstPost = $user->posts()->first();
        $firstPost->is_published = true;
        $firstPost->published_at = now();
        $firstPost->is_featured = true;
        $firstPost->save();
    }
}
