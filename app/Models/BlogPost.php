<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\BlogPostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\MarkdownService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ObservedBy(BlogPostObserver::class)]
/**
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_featured
 * @property-read User|null $author
 * @property-read MediaCollection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Database\Factories\BlogPostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogPost withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BlogPost extends Model implements HasMedia
{
    use HasFactory;
    use HasUlids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'published_at',
        'is_published',
        'is_featured',
        'user_id',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany<\App\Models\Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'blog_post_tag');
    }

    /**
     * @return HasMany<\App\Models\Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    /**
     * @return HasMany<\App\Models\Comment>
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class)->approved();
    }

    /**
     * Scope to get only published blog posts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    /**
     * Get the rendered HTML content from Markdown
     */
    public function getRenderedBodyAttribute(): string
    {
        return app(MarkdownService::class)->toHtml($this->body);
    }

    /**
     * Get the rendered HTML excerpt from Markdown (if excerpt exists)
     */
    public function getRenderedExcerptAttribute(): string
    {
        if (!$this->excerpt) {
            return '';
        }
        return app(MarkdownService::class)->toHtml($this->excerpt);
    }

    /**
     * Spatie Media Library configuration
     */
    public function registerMediaCollections(): void
    {
        /** @var string[] $mimeTypes */
        $mimeTypes = config('media-library.image_mime_types');
        $this->addMediaCollection('images')
            ->acceptsMimeTypes($mimeTypes);
    }

    /**
     * Add an image to the blog post
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function addImage(string|UploadedFile $image): Media
    {
        return $this->addMedia($image)
            ->withProperties(['owner_id' => $this->author_id, 'owner_type' => $this->author_type])
            ->toMediaCollection('images');
    }

    /**
     * Register the media conversions for the blog post
     * The preview conversion is used to display a preview of the image in a lower resolution
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->performOnCollections('images')
            ->nonQueued()
            ->width(300)
            ->height(300);
    }

    /**
     * Get the images associated with the blog post
     *
     * @return MediaCollection<string, Media>
     */
    public function images(): MediaCollection
    {
        return $this->getMedia('images');
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'published_at' => 'datetime',
        ];
    }
}
