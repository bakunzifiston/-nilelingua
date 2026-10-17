@props(['text'])

{{-- Renders content text with light inline markup: **bold**, *italic*, `code` and [label](/url). Everything else is escaped. --}}
@php
    $html = e($text);
    $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<em>$1</em>', $html);
    $html = preg_replace_callback('/\[([^\]]+)\]\((\/[^)\s]*|https?:\/\/[^)\s]+)\)/', fn ($m) => '<a href="'.$m[2].'">'.$m[1].'</a>', $html);
@endphp
{!! $html !!}
