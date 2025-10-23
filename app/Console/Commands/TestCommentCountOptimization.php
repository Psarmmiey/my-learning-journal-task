<?php

namespace App\Console\Commands;

use App\Http\Resources\BlogPost\FullBlogPostResource;
use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestCommentCountOptimization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:comment-count-optimization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that comment count optimization is working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing comment count optimization...');

        // Get a blog post with comments
        $post = BlogPost::with(['approvedComments'])->withCount('approvedComments')->first();

        if (!$post) {
            $this->error('No blog posts found. Please seed the database first.');
            return 1;
        }

        $this->info("Post: {$post->title}");
        $this->info("Approved comments count (database): {$post->approved_comments_count}");
        $this->info("Approved comments count (loaded collection): " . $post->approvedComments->count());

        // Test that they match
        if ($post->approved_comments_count === $post->approvedComments->count()) {
            $this->info('✅ Comment counts match!');
        } else {
            $this->error('❌ Comment counts don\'t match!');
        }

        // Test the FullBlogPostResource
        $resource = new FullBlogPostResource($post);
        $resourceArray = $resource->toArray(new Request());

        $this->info("Resource comments_count: " . $resourceArray['comments_count']);

        if ($resourceArray['comments_count'] === $post->approved_comments_count) {
            $this->info('✅ Resource uses optimized count!');
        } else {
            $this->error('❌ Resource not using optimized count!');
        }

        $this->info('Performance optimization test complete!');
        return 0;
    }
}
