<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->user_id;
    }

    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->user_id;
    }

    public function restore(User $user, BlogPost $blogPost): bool
    {

        return $user->id === $blogPost->user_id;
    }

    public function forceDelete(User $user, BlogPost $blogPost): bool
    {
        return $user->id === $blogPost->user_id;
    }
}
