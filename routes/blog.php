<?php

declare(strict_types=1);

use App\Http\Controllers\BlogPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogPostController::class, 'index'])->name('blog.index');
Route::middleware('auth')->group(function () {
    Route::get('/blog/my-posts', [BlogPostController::class, 'myPosts'])->name('blog.my-posts');
    Route::get('/blog/about', [BlogPostController::class, 'showAbout'])->name('blog.about');
    Route::post('/blog', [BlogPostController::class, 'store'])->name('blog.store');
    Route::post('/blog/{post}', [BlogPostController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [BlogPostController::class, 'destroy'])->name('blog.destroy');
});
Route::get('/blog/{post:slug}', [BlogPostController::class, 'show'])->name('blog.show');
