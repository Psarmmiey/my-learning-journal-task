<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view approved comments
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $comment->is_approved || $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $blogPost = null): bool
    {
        // Any authenticated user can create comments on published blog posts
        if ($blogPost) {
            return (bool) $blogPost->is_published;
        }
        
        return true; 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can only update their own comments within 15 minutes of creation
        return $comment->user_id === $user->id && 
               $comment->created_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can delete their own comments
        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Comment $comment): bool
    {
        // For now, any user can approve comments
        // In a real application, this would be limited to admins/moderators
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }
}
