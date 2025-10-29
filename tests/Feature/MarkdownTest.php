<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\User;
use App\Services\MarkdownService;

describe('Markdown Service', function () {
    test('converts basic markdown to HTML', function () {
        $service = app(MarkdownService::class);
        
        $markdown = '# Hello World\n\nThis is **bold** and *italic*.';
        $html = $service->toHtml($markdown);
        
        expect($html)->toContain('<h1')
                     ->toContain('Hello World')
                     ->toContain('<strong>bold</strong>')
                     ->toContain('<em>italic</em>');
    });

    test('converts markdown links safely', function () {
        $service = app(MarkdownService::class);
        
        $markdown = '[Safe link](https://example.com) and [Javascript link](javascript:alert("xss"))';
        $html = $service->toHtml($markdown);
        
        expect($html)->toContain('href="https://example.com"')
                     ->not->toContain('javascript:alert');
    });

    test('escapes HTML input', function () {
        $service = app(MarkdownService::class);
        
        $markdown = 'Normal text <script>alert("xss")</script> more text';
        $html = $service->toHtml($markdown);
        
        expect($html)->not->toContain('<script>')
                     ->toContain('&lt;script&gt;');
    });

    test('converts to plain text with limit', function () {
        $service = app(MarkdownService::class);
        
        $markdown = '# Title\n\nThis is a very long paragraph that should be truncated when converted to plain text with a specific character limit applied to it.';
        $plainText = $service->toPlainText($markdown, 50);
        
        expect($plainText)->toHaveLength(53) // 50 + "..."
                          ->toEndWith('...')
                          ->not->toContain('#')
                          ->not->toContain('<');
    });

    test('handles empty markdown gracefully', function () {
        $service = app(MarkdownService::class);
        
        expect($service->toHtml(''))->toBe('')
            ->and($service->toPlainText(''))->toBe('');
    });
});

describe('BlogPost Markdown Integration', function () {
    test('renders markdown content as HTML via attribute accessor', function () {
        $user = User::factory()->create();
        $post = BlogPost::factory()->create([
            'user_id' => $user->id,
            'body' => '# Test Title\n\nThis is **bold** text.',
            'excerpt' => 'This is *italic* excerpt.'
        ]);

        $renderedBody = $post->rendered_body;
        $renderedExcerpt = $post->rendered_excerpt;

        expect($renderedBody)->toContain('<h1')
                             ->toContain('Test Title')
                             ->toContain('<strong>bold</strong>');
        
        expect($renderedExcerpt)->toContain('<em>italic</em>');
    });

    test('blog post resources include both raw and rendered content', function () {
        $user = User::factory()->create();
        $post = BlogPost::factory()->create([
            'user_id' => $user->id,
            'body' => '# Test\n\n**Bold content**',
            'excerpt' => '*Italic excerpt*'
        ]);

        $resource = new \App\Http\Resources\BlogPost\FullBlogPostResource($post);
        $array = $resource->toArray(request());

        expect($array)
            ->toHaveKey('body', '# Test\n\n**Bold content**')
            ->toHaveKey('body_html')
            ->toHaveKey('excerpt', '*Italic excerpt*')
            ->toHaveKey('excerpt_html');

        expect($array['body_html'])->toContain('<h1')
                                   ->toContain('Test')
                                   ->toContain('<strong>Bold content</strong>');
        
        expect($array['excerpt_html'])->toContain('<em>Italic excerpt</em>');
    });
});