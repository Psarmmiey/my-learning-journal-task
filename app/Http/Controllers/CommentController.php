<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for a blog post.
     */
    public function index(Request $request, BlogPost $blogPost): AnonymousResourceCollection
    {
        $comments = $blogPost->approvedComments()
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created comment.
     */
    public function store(CreateCommentRequest $request, BlogPost $blogPost)
    {
        $comment = new Comment($request->validated());
        $comment->blog_post_id = $blogPost->id;
        $comment->user_id = $request->user()->id;
        $comment->save();

        $comment->load(['user', 'replies']);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment): CommentResource
    {
        $comment->load(['user', 'replies.user', 'parent.user']);
        
        return new CommentResource($comment);
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        Gate::authorize('update', $comment);

        $comment->update($request->validated());
        $comment->load(['user', 'replies']);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    /**
     * Reply to a comment.
     */
    public function reply(CreateCommentRequest $request, Comment $comment)
    {
        // Load the blog post relationship
        $comment->load('blogPost');
        
        // Authorization check
        Gate::authorize('create', [Comment::class, $comment->blogPost]);
        
        $reply = Comment::create([
            'content' => $request->validated()['content'],
            'blog_post_id' => $comment->blog_post_id,
            'user_id' => $request->user()->id,
            'parent_id' => $comment->id,
            'is_approved' => true, // Auto-approve replies for now
        ]);

        $reply->load(['user', 'parent.user']);

        return (new CommentResource($reply))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Approve a comment (admin only).
     */
    public function approve(Comment $comment): CommentResource
    {
        Gate::authorize('approve', $comment);

        $comment->update(['is_approved' => true]);
        $comment->load(['user', 'replies']);

        return new CommentResource($comment);
    }
}
