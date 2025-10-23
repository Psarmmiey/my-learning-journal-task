<?php

declare(strict_types=1);

namespace App\Http\Resources\BlogPost;

use App\Http\Resources\UserResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BlogPost */
class PreviewBlogPostResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     slug: string,
     *     excerpt: string,
     *     body: string,
     *     image: string|null,
     *     published_at: string,
     *     is_published: bool,
     *     author: UserResource,
     *     tags: array,
     *     created_at: string,
     *     updated_at: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'published_at' => $this->published_at,
            'is_published' => $this->is_published,
            'image' => $this->getFirstMedia('images')?->getFullUrl(),
            'author' => new UserResource($this->author),
            'tags' => $this->tags->pluck('name')->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
