<?php

declare(strict_types=1);

namespace App\Http\Resources\BlogPost;

use App\Http\Resources\UserResource;
use App\Models\BlogPost;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BlogPost */
class FullBlogPostResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     slug: string,
     *     excerpt: string,
     *     body: string,
     *     image: Media|null,
     *     published_at: string,
     *     is_published: bool,
     *     author: UserResource,
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
            'image' => $this->getFirstMedia('images'),
            'published_at' => $this->published_at,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'author' => new UserResource($this->author),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
