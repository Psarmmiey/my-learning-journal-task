<?php

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;

test('blog post page displays correct comment count including replies', function () {
    // Create a user and blog post
    $author = User::factory()->create();
    $user = User::factory()->create();
    $post = BlogPost::factory()->create([
        'user_id' => $author->id,
        'is_published' => true,
    ]);

    // Create some top-level comments
    $comment1 = Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => true,
        'parent_id' => null,
    ]);

    $comment2 = Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => true,
        'parent_id' => null,
    ]);

    // Create some replies
    $reply1 = Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => true,
        'parent_id' => $comment1->id,
    ]);

    $reply2 = Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => true,
        'parent_id' => $comment1->id,
    ]);

    // Create one unapproved comment (should not be counted)
    Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => false,
        'parent_id' => null,
    ]);

    // Visit the blog post page
    $response = $this->get("/blog/{$post->slug}");

    $response->assertOk();

    // Check that the response includes the correct comment count (4 approved comments total)
    $response->assertInertia(fn ($assert) => $assert
        ->component('Blog/Show')
        ->where('post.data.comments_count', 4) // 2 top-level + 2 replies = 4 approved comments
    );
});

test('comment count is zero when no comments exist', function () {
    $author = User::factory()->create();
    $post = BlogPost::factory()->create([
        'user_id' => $author->id,
        'is_published' => true,
    ]);

    $response = $this->get("/blog/{$post->slug}");

    $response->assertOk();
    $response->assertInertia(fn ($assert) => $assert
        ->component('Blog/Show')
        ->where('post.data.comments_count', 0)
    );
});

test('comment count excludes unapproved comments', function () {
    $author = User::factory()->create();
    $user = User::factory()->create();
    $post = BlogPost::factory()->create([
        'user_id' => $author->id,
        'is_published' => true,
    ]);

    // Create approved and unapproved comments
    Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => true,
    ]);

    Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => false,
    ]);

    Comment::factory()->create([
        'blog_post_id' => $post->id,
        'user_id' => $user->id,
        'is_approved' => false,
    ]);

    $response = $this->get("/blog/{$post->slug}");

    $response->assertOk();
    $response->assertInertia(fn ($assert) => $assert
        ->component('Blog/Show')
        ->where('post.data.comments_count', 1) // Only 1 approved comment
    );
});