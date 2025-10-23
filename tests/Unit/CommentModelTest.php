<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('comment has ulid primary key', function () {
    $comment = Comment::factory()->create();

    expect($comment->id)->toBeString();
    expect(strlen($comment->id))->toBe(26); // ULID length
});

test('comment belongs to a blog post', function () {
    $blogPost = BlogPost::factory()->create();
    $comment = Comment::factory()->create(['blog_post_id' => $blogPost->id]);

    expect($comment->blogPost)->toBeInstanceOf(BlogPost::class);
    expect($comment->blogPost->id)->toBe($blogPost->id);
});

test('comment belongs to a user', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->user)->toBeInstanceOf(User::class);
    expect($comment->user->id)->toBe($user->id);
});

test('comment can have a parent comment', function () {
    $parentComment = Comment::factory()->create();
    $replyComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

    expect($replyComment->parent)->toBeInstanceOf(Comment::class);
    expect($replyComment->parent->id)->toBe($parentComment->id);
});

test('comment can have multiple replies', function () {
    $parentComment = Comment::factory()->create();
    $reply1 = Comment::factory()->create(['parent_id' => $parentComment->id]);
    $reply2 = Comment::factory()->create(['parent_id' => $parentComment->id]);

    $replies = $parentComment->replies;
    
    expect($replies)->toHaveCount(2);
    expect($replies->pluck('id'))->toContain($reply1->id);
    expect($replies->pluck('id'))->toContain($reply2->id);
});

test('isReply method returns true when comment has parent', function () {
    $parentComment = Comment::factory()->create();
    $replyComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

    expect($replyComment->isReply())->toBeTrue();
});

test('isReply method returns false when comment has no parent', function () {
    $comment = Comment::factory()->create(['parent_id' => null]);

    expect($comment->isReply())->toBeFalse();
});

test('approved scope returns only approved comments', function () {
    Comment::factory()->count(3)->create(['is_approved' => true]);
    Comment::factory()->count(2)->create(['is_approved' => false]);

    $approvedComments = Comment::approved()->get();

    expect($approvedComments)->toHaveCount(3);
    $approvedComments->each(function ($comment) {
        expect($comment->is_approved)->toBeTrue();
    });
});

test('comment content is fillable', function () {
    $comment = new Comment([
        'content' => 'Test comment content',
        'blog_post_id' => 'test-id',
        'user_id' => 'user-id',
    ]);

    expect($comment->content)->toBe('Test comment content');
    expect($comment->blog_post_id)->toBe('test-id');
    expect($comment->user_id)->toBe('user-id');
});

test('comment casts is_approved to boolean', function () {
    $comment = Comment::factory()->create(['is_approved' => 1]);

    expect($comment->is_approved)->toBeBool();
    expect($comment->is_approved)->toBeTrue();
});

test('comment timestamps are cast to datetime', function () {
    $comment = Comment::factory()->create();

    expect($comment->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($comment->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
