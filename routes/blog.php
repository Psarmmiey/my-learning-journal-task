<?php

declare(strict_types=1);

use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogPostController::class, 'index'])->name('blog.index');
Route::get('/blog/about', [BlogPostController::class, 'showAbout'])->name('blog.about');
Route::middleware('auth')->group(function () {
    Route::get('/blog/my-posts', [BlogPostController::class, 'myPosts'])->name('blog.my-posts');
    Route::post('/blog', [BlogPostController::class, 'store'])->name('blog.store');
    Route::post('/blog/{post}', [BlogPostController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [BlogPostController::class, 'destroy'])->name('blog.destroy');
    
    // Comment routes
    Route::post('/blog/{blogPost}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
});

// Public comment routes
Route::get('/blog/{blogPost}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('comments.show');

Route::get('/blog/{post:slug}', [BlogPostController::class, 'show'])->name('blog.show');
