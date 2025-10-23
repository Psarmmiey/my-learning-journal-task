<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $users = User::all();
        $blogPosts = BlogPost::published()->take(10)->get();
        
        foreach ($blogPosts as $post) {
            // Create 2-5 top-level comments per post
            $topLevelComments = Comment::factory()
                ->count(rand(2, 5))
                ->create([
                    'blog_post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);
            
            // Create some replies for each top-level comment (30% chance)
            foreach ($topLevelComments as $comment) {
                if (rand(1, 100) <= 30) {
                    Comment::factory()
                        ->count(rand(1, 2))
                        ->reply($comment)
                        ->create([
                            'user_id' => $users->random()->id,
                        ]);
                }
            }
        }

        // Create a few pending (unapproved) comments
        Comment::factory()
            ->count(3)
            ->pending()
            ->create([
                'blog_post_id' => $blogPosts->random()->id,
                'user_id' => $users->random()->id,
            ]);
    }
}
