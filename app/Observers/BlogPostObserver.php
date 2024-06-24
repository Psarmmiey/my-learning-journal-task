<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Support\Str;

/** @mixin BlogPost */
class BlogPostObserver
{
    public function creating(BlogPost $blogPost): void
    {
        $blogPost->slug = $this->generateSlug($blogPost->title);

        if ($blogPost->is_featured) {
            BlogPost::where('is_featured', true)
                ->update(['is_featured' => false]);
        }
        if ($blogPost->is_published) {
            $blogPost->published_at = Carbon::now();
        }
    }

    public function updating(BlogPost $blogPost): void
    {
        if ($blogPost->isDirty('title')) {
            $blogPost->slug = $this->generateSlug($blogPost->title);
            $blogPost->published_at = Carbon::now();
        }

        if ($blogPost->isDirty('is_published') && $blogPost->is_published) {
            $blogPost->published_at = Carbon::now();
        } elseif ($blogPost->isDirty('is_published') && ! $blogPost->is_published) {
            $blogPost->published_at = null;
            if ($blogPost->is_featured) {
                $blogPost->is_featured = false;
                $latestPost = BlogPost::where('is_published', true)
                    ->latest('published_at')
                    ->first();
                if ($latestPost) {
                    $latestPost->is_featured = true;
                    $latestPost->save();
                }
            }
        }

        if ($blogPost->isDirty('is_featured') && $blogPost->is_featured) {
            BlogPost::where('is_featured', true)
                ->update(['is_featured' => false]);
        }
    }

    protected function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$i++;
        }

        return $slug;
    }
}
