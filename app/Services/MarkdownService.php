<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\LaravelMarkdown\MarkdownRenderer;

class MarkdownService
{
    public function __construct(private MarkdownRenderer $renderer)
    {
    }

    /**
     * Convert markdown to HTML with security measures
     */
    public function toHtml(string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // First render markdown to HTML
        $html = $this->renderer->toHtml($markdown);

        // Additional sanitization if needed
        // The Spatie package already handles most security concerns with proper configuration
        return $html;
    }

    /**
     * Generate a plain text excerpt from markdown
     */
    public function toPlainText(string $markdown, int $limit = 200): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Convert to HTML first, then strip tags for plain text
        $html = $this->toHtml($markdown);
        $plainText = strip_tags($html);
        
        // Truncate and add ellipsis if needed
        if (strlen($plainText) > $limit) {
            return substr($plainText, 0, $limit) . '...';
        }
        
        return $plainText;
    }
}