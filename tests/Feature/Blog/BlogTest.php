<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('users can see blog posts', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('users can see a single blog post', function () {
    $blogPost = BlogPost::factory()->create();
    $response = $this->get('/blog/'.$blogPost->slug);

    $response->assertStatus(200);

    $response->assertSee($blogPost->title);
    $response->assertSee($blogPost->body);

});

test('users can not see a non-existing blog post', function () {
    $response = $this->get('/blog/non-existing-slug');

    $response->assertStatus(404);
});

test('logged in users can create a blog post', function () {
    $user = User::factory()->create();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('image.jpg');

    $response = $this
        ->actingAs($user)
        ->post('/blog', [
            'title' => 'Test Blog Post',
            'body' => 'This is a test blog post',
            'excerpt' => 'This is a test blog post excerpt',
            'published_at' => '2021-01-01 00:00:00',
            'is_published' => true,
            'is_featured' => true,
            'photo' => $file,
        ]);

    $response->assertOk();
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('blog_posts', [
        'title' => 'Test Blog Post',
        'body' => 'This is a test blog post',
    ]);
});

test('logged in users can update a blog post', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/blog/'.$blogPost->id, [
            'title' => 'Updated Blog Post',
            'body' => 'This is an updated blog post',
            'excerpt' => 'This is an updated blog post excerpt',
            'published_at' => '2021-01-01 00:00:00',
            'is_published' => true,
            'is_featured' => true,
        ]);

    $response->assertOk();
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('blog_posts', [
        'title' => 'Updated Blog Post',
        'body' => 'This is an updated blog post',
    ]);
});

test('logged in users can delete a blog post', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/blog/'.$blogPost->id);

    $response->assertOk();
    $response->assertSessionHasNoErrors();

    $this->assertSoftDeleted('blog_posts', [
        'id' => $blogPost->id,
    ]);
});

test('logged in users can see their own blog posts', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/blog/my-posts');

    $response->assertOk();
    $response->assertSee($blogPost->title);
});
