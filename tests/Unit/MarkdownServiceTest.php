<?php

declare(strict_types=1);

use App\Services\MarkdownService;
use Spatie\LaravelMarkdown\MarkdownRenderer;

describe('MarkdownService Unit Tests', function () {
    beforeEach(function () {
        $this->renderer = Mockery::mock(MarkdownRenderer::class);
        $this->service = new MarkdownService($this->renderer);
    });

    test('delegates to renderer for HTML conversion', function () {
        $markdown = '# Test';
        $expectedHtml = '<h1>Test</h1>';
        
        $this->renderer
            ->shouldReceive('toHtml')
            ->once()
            ->with($markdown)
            ->andReturn($expectedHtml);

        $result = $this->service->toHtml($markdown);
        
        expect($result)->toBe($expectedHtml);
    });

    test('returns empty string for empty markdown', function () {
        $result = $this->service->toHtml('');
        
        expect($result)->toBe('');
    });

    test('converts HTML to plain text and truncates', function () {
        $markdown = '# Title\n\nContent here';
        $html = '<h1>Title</h1><p>Content here</p>';
        
        $this->renderer
            ->shouldReceive('toHtml')
            ->once()
            ->with($markdown)
            ->andReturn($html);

        $result = $this->service->toPlainText($markdown, 10);
        
        expect($result)->toBe('TitleConte...');
    });

    test('does not truncate if under limit', function () {
        $markdown = 'Short';
        $html = '<p>Short</p>';
        
        $this->renderer
            ->shouldReceive('toHtml')
            ->once()
            ->with($markdown)
            ->andReturn($html);

        $result = $this->service->toPlainText($markdown, 100);
        
        expect($result)->toBe('Short');
    });
});