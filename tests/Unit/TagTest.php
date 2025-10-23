<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\BlogPost;

test('tag model has correct fillable attributes', function () {
    $tag = new Tag();
    expect($tag->getFillable())->toBe(['name', 'slug']);
});

test('tag model uses ULID primary key', function () {
    $tag = Tag::factory()->create();
    expect($tag->getKeyType())->toBe('string');
    expect(strlen($tag->getKey()))->toBe(26); // ULID length
});

test('tag can have many blog posts', function () {
    $tag = Tag::factory()->create();
    $posts = BlogPost::factory()->count(3)->create();
    
    foreach ($posts as $post) {
        $tag->posts()->attach($post->id);
    }
    
    expect($tag->posts)->toHaveCount(3);
});

test('tag slug is stored correctly', function () {
    $tag = Tag::factory()->create([
        'name' => 'Laravel Framework',
        'slug' => 'laravel-framework'
    ]);
    
    expect($tag->slug)->toBe('laravel-framework');
    expect($tag->name)->toBe('Laravel Framework');
});