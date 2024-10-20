<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\BlogPost\FullBlogPostResource;
use App\Http\Resources\BlogPost\PreviewBlogPostResource;
use App\Models\BlogPost;
use App\Services\BlogPostServices;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BlogPostController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly BlogPostServices $blogPostServices) {}

    public function index(): Response
    {
        $blogPosts = $this->blogPostServices
            ->allBlogPosts();

        $featuredPost = $this->blogPostServices->findFeaturedPost();

        return Inertia::render('Blog/Index', [
            'blogPosts' => PreviewBlogPostResource::collection($blogPosts),
            'featuredPost' => $featuredPost ? PreviewBlogPostResource::make($featuredPost) : null,
        ]);

    }

    public function myPosts(Request $request): Response
    {
        $user = auth()->user();
        $filter = $request->query('filter');
        $blogPosts = $this->blogPostServices
            ->myBlogPosts($user, $filter);

        return Inertia::render('Blog/MyPosts', [
            'blogPosts' => FullBlogPostResource::collection($blogPosts),
        ]);
    }

    public function store(CreateBlogPostRequest $request): void
    {
        $user = auth()->user();
        $this->authorize('create', BlogPost::class);
        $post = $user->posts()->create($request->validated());

        $image = $request->file('photo');
        $post->addImage($image);
    }

    public function show(BlogPost $post): Response
    {
        $recentPosts = $this->blogPostServices
            ->recentBlogPosts(3, $post)->get();

        return Inertia::render('Blog/Show', [
            'post' => FullBlogPostResource::make($post),
            'recentPosts' => PreviewBlogPostResource::collection($recentPosts),
        ]);
    }

    public function showAbout(): Response
    {
        $recentPosts = $this->blogPostServices
            ->recentBlogPosts(3)->get();

        return Inertia::render('Blog/About', [
            'recentPosts' => PreviewBlogPostResource::collection($recentPosts),
        ]);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $post): void
    {
        Gate::authorize('update', $post);

        $post->update($request->validated());
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $post->clearMediaCollection('images');
            $post->addImage($image);
        }
    }

    public function destroy(BlogPost $post): void
    {
        Gate::authorize('delete', $post);
        $post->delete();
    }
}
