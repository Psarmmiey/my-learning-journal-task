<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;

test('users can create comments on published blog posts', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->published()->create();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('comments.store', $blogPost), [
            'content' => 'This is a great blog post! Thanks for sharing.',
        ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'data' => [
            'id',
            'content',
            'is_approved',
            'is_reply',
            'created_at',
            'user' => ['id', 'name'],
        ]
    ]);

    expect(Comment::count())->toBe(1);
    
    $comment = Comment::first();
    expect($comment->content)->toBe('This is a great blog post! Thanks for sharing.');
    expect($comment->blog_post_id)->toBe($blogPost->id);
    expect($comment->user_id)->toBe($user->id);
    expect($comment->is_approved)->toBeTrue();
});

test('comment content must be at least 3 characters', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->published()->create();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('comments.store', $blogPost), [
            'content' => 'Hi',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('content');
});

test('comment content cannot exceed 2000 characters', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->published()->create();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('comments.store', $blogPost), [
            'content' => str_repeat('a', 2001),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('content');
});

test('users can reply to comments', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->published()->create();
    $parentComment = Comment::factory()->create([
        'blog_post_id' => $blogPost->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('comments.reply', $parentComment), [
            'content' => 'This is a reply to the comment.',
        ]);

    $response->assertStatus(201);
    
    expect(Comment::count())->toBe(2);
    
    $reply = Comment::where('parent_id', $parentComment->id)->first();
    expect($reply->content)->toBe('This is a reply to the comment.');
    expect($reply->parent_id)->toBe($parentComment->id);
    expect($reply->blog_post_id)->toBe($blogPost->id);
    expect($reply->isReply())->toBeTrue();
});

test('users can update their own comments within 15 minutes', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'content' => 'Original comment',
        'created_at' => now()->subMinutes(10), // 10 minutes ago
    ]);

    $response = $this->actingAs($user)->patch(route('comments.update', $comment), [
        'content' => 'Updated comment content',
    ]);

    $response->assertOk();
    
    $comment->refresh();
    expect($comment->content)->toBe('Updated comment content');
});

test('users cannot update comments after 15 minutes', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'content' => 'Original comment',
        'created_at' => now()->subMinutes(20), // 20 minutes ago
    ]);

    $response = $this->actingAs($user)->patch(route('comments.update', $comment), [
        'content' => 'Updated comment content',
    ]);

    $response->assertStatus(403);
    
    $comment->refresh();
    expect($comment->content)->toBe('Original comment');
});

test('users cannot update other users comments', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user1->id,
        'content' => 'Original comment',
    ]);

    $response = $this->actingAs($user2)->patch(route('comments.update', $comment), [
        'content' => 'Hacked comment',
    ]);

    $response->assertStatus(403);
    
    $comment->refresh();
    expect($comment->content)->toBe('Original comment');
});

test('users can delete their own comments', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

    $response->assertOk();
    $response->assertJson(['message' => 'Comment deleted successfully.']);
    
    expect(Comment::count())->toBe(0);
});

test('users cannot delete other users comments', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user1->id]);

    $response = $this->actingAs($user2)->delete(route('comments.destroy', $comment));

    $response->assertStatus(403);
    
    expect(Comment::count())->toBe(1);
});

test('can list comments for a blog post', function () {
    $blogPost = BlogPost::factory()->published()->create();
    $comments = Comment::factory()->count(5)->create([
        'blog_post_id' => $blogPost->id,
        'is_approved' => true,
    ]);

    $response = $this->get(route('comments.index', $blogPost));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'content',
                'is_approved',
                'created_at',
                'user' => ['id', 'name'],
            ]
        ]
    ]);
    
    expect($response->json('data'))->toHaveCount(5);
});

test('only approved comments are shown in public listing', function () {
    $blogPost = BlogPost::factory()->published()->create();
    
    // Create approved and unapproved comments
    Comment::factory()->count(3)->create([
        'blog_post_id' => $blogPost->id,
        'is_approved' => true,
    ]);
    
    Comment::factory()->count(2)->create([
        'blog_post_id' => $blogPost->id,
        'is_approved' => false,
    ]);

    $response = $this->get(route('comments.index', $blogPost));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

test('comments are ordered by creation date desc', function () {
    $blogPost = BlogPost::factory()->published()->create();
    
    $oldComment = Comment::factory()->create([
        'blog_post_id' => $blogPost->id,
        'created_at' => now()->subDay(),
        'content' => 'Old comment',
    ]);
    
    $newComment = Comment::factory()->create([
        'blog_post_id' => $blogPost->id,
        'created_at' => now(),
        'content' => 'New comment',
    ]);

    $response = $this->get(route('comments.index', $blogPost));

    $response->assertOk();
    $comments = $response->json('data');
    
    expect($comments[0]['content'])->toBe('New comment');
    expect($comments[1]['content'])->toBe('Old comment');
});

test('guests cannot create comments', function () {
    $blogPost = BlogPost::factory()->published()->create();

    $response = $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('comments.store', $blogPost), [
            'content' => 'Anonymous comment',
        ]);

    $response->assertStatus(401);
    expect(Comment::count())->toBe(0);
});

test('users can approve comments', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->pending()->create();

    $response = $this->actingAs($user)->patch(route('comments.approve', $comment));

    $response->assertOk();
    
    $comment->refresh();
    expect($comment->is_approved)->toBeTrue();
});
