<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can create a blog post with tags', function () {
    $tagData = ['Laravel', 'PHP', 'Tutorial'];
    
    $response = $this->post('/blog', [
        'title' => 'Test Post',
        'body' => 'Test content',
        'is_published' => true,
        'is_featured' => false,
        'tags' => $tagData,
        'photo' => \Illuminate\Http\UploadedFile::fake()->image('test.png')
    ]);
    
    $post = BlogPost::where('title', 'Test Post')->first();
    expect($post)->not->toBeNull();
    expect($post->tags->pluck('name')->toArray())->toBe($tagData);
});

test('can update blog post tags', function () {
    $post = BlogPost::factory()->create(['user_id' => $this->user->id]);
    $originalTags = ['Original', 'Tags'];
    $newTags = ['Updated', 'New', 'Tags'];
    
    // Create original tags
    foreach ($originalTags as $tagName) {
        $tag = Tag::factory()->create(['name' => $tagName]);
        $post->tags()->attach($tag->id);
    }
    
    $response = $this->post('/blog/' . $post->id, [
        'title' => 'Updated Post',
        'excerpt' => 'Updated excerpt',
        'tags' => $newTags,
    ]);
    
    $response->assertOk();
    $response->assertSessionHasNoErrors();
    
    $post->refresh();
    expect($post->tags->pluck('name')->sort()->values()->toArray())->toBe(collect($newTags)->sort()->values()->toArray());
});

test('tags are created automatically if they dont exist', function () {
    $nonExistentTags = ['New Tag 1', 'New Tag 2'];
    
    expect(Tag::whereIn('name', $nonExistentTags)->count())->toBe(0);
    
    $this->post('/blog', [
        'title' => 'Test Post',
        'body' => 'Test content',
        'is_published' => true,
        'is_featured' => false,
        'tags' => $nonExistentTags,
        'photo' => \Illuminate\Http\UploadedFile::fake()->image('test.png')
    ]);
    
    expect(Tag::whereIn('name', $nonExistentTags)->count())->toBe(2);
});

test('tag slugs are generated correctly', function () {
    $tagName = 'Complex Tag Name With Spaces';
    $expectedSlug = 'complex-tag-name-with-spaces';
    
    $this->post('/blog', [
        'title' => 'Test Post',
        'body' => 'Test content',
        'is_published' => true,
        'is_featured' => false,
        'tags' => [$tagName],
        'photo' => \Illuminate\Http\UploadedFile::fake()->image('test.png')
    ]);
    
    $tag = Tag::where('name', $tagName)->first();
    expect($tag->slug)->toBe($expectedSlug);
});

test('can filter blog posts by tag', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $taggedPost = BlogPost::factory()->create([
        'user_id' => $this->user->id,
        'is_published' => true
    ]);
    $untaggedPost = BlogPost::factory()->create([
        'user_id' => $this->user->id,
        'is_published' => true
    ]);
    
    $taggedPost->tags()->attach($tag->id);
    
    $service = new \App\Services\BlogPostServices();
    $results = $service->blogPostsByTag('laravel');
    
    expect($results->items())->toHaveCount(1);
    expect($results->items()[0]->id)->toBe($taggedPost->id);
});