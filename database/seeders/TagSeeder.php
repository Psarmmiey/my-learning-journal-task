<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Vue.js', 'slug' => 'vuejs'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Tutorial', 'slug' => 'tutorial'],
            ['name' => 'Programming', 'slug' => 'programming'],
            ['name' => 'Frontend', 'slug' => 'frontend'],
            ['name' => 'Backend', 'slug' => 'backend'],
            ['name' => 'Database', 'slug' => 'database'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}