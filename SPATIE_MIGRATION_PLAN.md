# Migration Plan: Custom Comments → Spatie Laravel Comments

## Overview

This migration will replace our custom comment system with Spatie's Laravel Comments package, which provides:
- Better maintainability and community support
- Built-in features like threaded comments, reactions, and moderation
- Consistent API design following Laravel conventions
- Comprehensive testing and documentation

## Phase 1: Installation (Manual Steps Required)

### 1. Install Package
```bash
composer require spatie/laravel-comments
```

### 2. Publish and Run Migrations
```bash
php artisan vendor:publish --tag="comments-migrations"
php artisan migrate
```

### 3. Optional: Publish Config
```bash
php artisan vendor:publish --tag="comments-config"
```

## Phase 2: Model Updates

### Update BlogPost Model
Add the Spatie comments functionality to your BlogPost model:

```php
<?php

namespace App\Models;

use Spatie\Comments\Models\Concerns\HasComments;
use Spatie\Comments\Models\Concerns\Interfaces\CanComment;

class BlogPost extends Model implements CanComment
{
    use HasComments;
    
    // ... existing code
}
```

### Update User Model  
```php
<?php

namespace App\Models;

use Spatie\Comments\Models\Concerns\InteractsWithComments;
use Spatie\Comments\Models\Concerns\Interfaces\CanComment;

class User extends Authenticatable implements CanComment
{
    use InteractsWithComments;
    
    // ... existing code
}
```

## Phase 3: Data Migration

### Custom Migration to Preserve Existing Data
Create a migration to transfer data from our custom comments table to Spatie's structure:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Comment as CustomComment;
use Spatie\Comments\Models\Comment as SpatieComment;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate top-level comments first
        CustomComment::whereNull('parent_id')->chunk(100, function ($comments) {
            foreach ($comments as $comment) {
                $spatieComment = SpatieComment::create([
                    'commentable_type' => 'App\Models\BlogPost',
                    'commentable_id' => $comment->blog_post_id,
                    'commenter_type' => 'App\Models\User', 
                    'commenter_id' => $comment->user_id,
                    'comment' => $comment->content,
                    'approved_at' => $comment->is_approved ? $comment->created_at : null,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ]);
                
                // Store mapping for replies
                $this->commentMapping[$comment->id] = $spatieComment->id;
            }
        });
        
        // Then migrate replies
        CustomComment::whereNotNull('parent_id')->chunk(100, function ($comments) {
            foreach ($comments as $comment) {
                $parentId = $this->commentMapping[$comment->parent_id] ?? null;
                if ($parentId) {
                    SpatieComment::create([
                        'commentable_type' => 'App\Models\BlogPost',
                        'commentable_id' => $comment->blog_post_id,
                        'commenter_type' => 'App\Models\User',
                        'commenter_id' => $comment->user_id,
                        'comment' => $comment->content,
                        'parent_id' => $parentId,
                        'approved_at' => $comment->is_approved ? $comment->created_at : null,
                        'created_at' => $comment->created_at,
                        'updated_at' => $comment->updated_at,
                    ]);
                }
            }
        });
    }
};
```

## Phase 4: Controller Updates

### Replace CommentController with Spatie's API
Spatie provides built-in controllers, but you can customize them:

```php
<?php

namespace App\Http\Controllers;

use Spatie\Comments\Http\Controllers\CommentController as SpatieCommentController;

class CommentController extends SpatieCommentController
{
    // Override methods as needed for custom business logic
    
    public function store(Request $request)
    {
        // Add custom validation or authorization
        return parent::store($request);
    }
}
```

## Phase 5: Frontend Updates

### Update Vue Components to use Spatie's API

The Spatie package provides these endpoints:
- `POST /comments` - Create comment
- `PATCH /comments/{comment}` - Update comment  
- `DELETE /comments/{comment}` - Delete comment
- `GET /comments` - List comments

### Updated CommentSection.vue
```vue
<script setup>
// Update API calls to use Spatie's endpoints
const fetchComments = async () => {
    const response = await axios.get('/comments', {
        params: {
            commentable_type: 'App\\Models\\BlogPost',
            commentable_id: props.blogPostId
        }
    })
    comments.value = response.data.data
}

const createComment = async (content) => {
    const response = await axios.post('/comments', {
        commentable_type: 'App\\Models\\BlogPost',
        commentable_id: props.blogPostId,
        comment: content
    })
    return response.data
}
</script>
```

## Phase 6: Route Updates

### Update routes/blog.php
```php
<?php

use Spatie\Comments\Http\Controllers\CommentController;

// Replace custom comment routes with Spatie's
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
```

## Phase 7: Policy Updates

### Update CommentPolicy for Spatie's Comment Model
```php
<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Comments\Models\Comment;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->commenter_id 
            && $comment->created_at->diffInMinutes(now()) <= 15;
    }
    
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->commenter_id;
    }
}
```

## Phase 8: Test Updates

### Update Tests for Spatie's Models
```php
<?php

use Spatie\Comments\Models\Comment;

test('users can create comments', function () {
    $user = User::factory()->create();
    $post = BlogPost::factory()->create();
    
    $response = $this->actingAs($user)->post('/comments', [
        'commentable_type' => 'App\\Models\\BlogPost',
        'commentable_id' => $post->id,
        'comment' => 'Test comment'
    ]);
    
    $response->assertCreated();
    expect(Comment::count())->toBe(1);
});
```

## Phase 9: Cleanup

### Remove Custom Implementation
1. Delete custom Comment model: `app/Models/Comment.php`
2. Delete custom CommentController: `app/Http/Controllers/CommentController.php`
3. Delete custom migrations: `database/migrations/*_create_comments_table.php`
4. Delete custom factories and seeders
5. Update any remaining references

## Benefits of Migration

1. **Maintenance**: Let Spatie handle package updates and bug fixes
2. **Features**: Get additional features like reactions, mentions, etc.
3. **Testing**: Leverage Spatie's comprehensive test suite
4. **Community**: Benefit from community contributions and support
5. **Performance**: Optimized queries and caching strategies

## Potential Challenges

1. **Data Migration**: Need to carefully map existing data
2. **Custom Features**: May need to extend Spatie's implementation
3. **Frontend**: Need to update Vue components to match new API
4. **Testing**: All existing tests will need updates

Would you like me to start implementing any specific phase of this migration?