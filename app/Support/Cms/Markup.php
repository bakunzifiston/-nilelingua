<?php

namespace App\Support\Cms;

/**
 * Light, safe inline markup for editable content.
 *
 * Editors write plain text; *words* become emphasis (the gold italic accent in headings),
 * **words** become bold and line breaks are kept. Everything else is escaped.
 */
class Markup
{
    public static function inline(?string $text): string
    {
        $html = e((string) $text);
        $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
        $html = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<em>$1</em>', $html);

        return nl2br($html, false);
    }

    /** A tel: link target built from a displayed phone number. */
    public static function tel(?string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', (string) $phone);
    }

    /** Plain text version (markup removed), for titles, attributes and structured data. */
    public static function plain(?string $text): string
    {
        return trim(preg_replace(['/\*\*(.+?)\*\*/s', '/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '/\s*\n\s*/'], ['$1', '$1', ' '], (string) $text));
    }

    /**
     * Interface strings may carry a few formatting tags; anything else is stripped on save.
     */
    public static function sanitizeInterfaceHtml(string $text): string
    {
        $clean = strip_tags($text, '<em><strong><br><b><i>');

        // Remove any attributes from the allowed tags.
        return preg_replace('/<(em|strong|br|b|i)\b[^>]*>/i', '<$1>', $clean);
    }
}
