<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use UnexpectedValueException;

class BlogPostSeeder extends Seeder
{
    /**
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function run(): void
    {
        $users = User::all();
        $sampleImages = [
            'blog1.jpg',
            'blog2.jpg',
            'blog3.jpg',
            'blog4.jpg',
            'blog5.jpg',
        ];

        if ($users->count() === 0) {
            throw new UnexpectedValueException('No user record found');
        }

        foreach ($users as $user) {
            $factory = \App\Models\BlogPost::factory();

            $posts = $factory
                ->count(20)
                ->create([
                    'user_id' => $user->getKey(),
                ]);

            foreach ($posts as $post) {
                // random hd image from picsum at 1920x1080
                try {
                    $post->addMediaFromUrl('https://picsum.photos/1920/1080')
                        ->toMediaCollection('images');
                } catch (FileCannotBeAdded | FileDoesNotExist | FileIsTooBig $e) {
                    $post->addMediaFromDisk(
                        storage_path('app/public/' . $sampleImages[array_rand($sampleImages)])
                    )->toMediaCollection('images');
                }
//                $post->addMediaFromUrl('https://picsum.photos/1920/1080')
//                    ->toMediaCollection('images');
            }
        }

        $user = User::first();
        $firstPost = $user->posts()->latest()->first();
        $firstPost->is_published = true;
        $firstPost->published_at = now();
        $firstPost->is_featured = true;
        $firstPost->save();
    }
}
