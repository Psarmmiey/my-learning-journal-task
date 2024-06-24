<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BlogPostServices
{
    public function allBlogPosts(): Builder
    {
        return BlogPost::orderBy('published_at', 'desc')
            ->with('author')
            ->where('is_published', true);
    }

    public function recentBlogPosts(int $count = 3, ?BlogPost $currentPost = null): Builder
    {
        return BlogPost::orderBy('published_at', 'desc')
            ->with('author')
            ->where('is_published', true)
            ->when($currentPost, fn ($query) => $query->where('id', '!=', $currentPost->id))
            ->limit($count);
    }

    public function myBlogPosts(User $user, $filter = 'all'): array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\LaravelIdea\Helper\App\Models\_IH_BlogPost_C|\Illuminate\Pagination\LengthAwarePaginator
    {
        return $user->posts()
            ->latest('id')
            ->when($filter === 'published', fn ($query) => $query->where('is_published', true))
            ->when($filter === 'draft', fn ($query) => $query->where('is_published', false))
            ->paginate(10, ['*'], 'blogPosts');
    }

    public function findFeaturedPost(): ?BlogPost
    {
        return BlogPost::where('is_featured', true)
            ->where('is_published', true)
            ->first();
    }
}
