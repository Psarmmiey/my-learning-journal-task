<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use LaravelIdea\Helper\App\Models\_IH_BlogPost_C;

class BlogPostServices
{
    /**
     * Fetch all blog posts that are published and order by published date
     */
    public function allBlogPosts(): CursorPaginator
    {
        return BlogPost::orderBy('published_at', 'desc')
            ->with('author')
            ->where('is_published', true)
            ->latest('id')
            ->cursorPaginate(10, ['*'], 'blogPosts');
    }

    /**
     * Fetch recent blog posts
     */
    public function recentBlogPosts(int $count = 3, ?BlogPost $currentPost = null): Builder
    {
        return BlogPost::orderBy('published_at', 'desc')
            ->with('author')
            ->where('is_published', true)
            ->when($currentPost, fn ($query) => $query->where('id', '!=', $currentPost->id))
            ->limit($count);
    }

    /**
     * Blog posts created by the user
     */
    public function myBlogPosts(User $user, $filter = 'all'): array|LengthAwarePaginator|_IH_BlogPost_C|\Illuminate\Pagination\LengthAwarePaginator
    {
        return $user->posts()
            ->latest('id')
            ->when($filter === 'published', fn ($query) => $query->where('is_published', true))
            ->when($filter === 'draft', fn ($query) => $query->where('is_published', false))
            ->paginate(10, ['*'], 'blogPosts');
    }

    /**
     * Find a featured blog post
     */
    public function findFeaturedPost(): ?BlogPost
    {
        return BlogPost::where('is_featured', true)
            ->where('is_published', true)
            ->first();
    }

    /**
     * Fetch blog posts filtered by tag
     */
    public function blogPostsByTag(string $tagSlug): CursorPaginator
    {
        return BlogPost::whereHas('tags', function ($query) use ($tagSlug) {
            $query->where('slug', $tagSlug);
        })
            ->where('is_published', true)
            ->with(['author', 'tags'])
            ->orderBy('published_at', 'desc')
            ->latest('id')
            ->cursorPaginate(10, ['*'], 'blogPosts');
    }

    /**
     * Get popular tags with post count
     */
    public function getPopularTags(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Tag::withCount(['posts' => function ($query) {
            $query->where('is_published', true);
        }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
