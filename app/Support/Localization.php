<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

/**
 * English is served at the site root; French lives under the /fr prefix with the same paths.
 */
class Localization
{
    public const DEFAULT = 'en';

    public const LOCALES = [
        'en' => ['name' => 'English', 'short' => 'EN', 'prefix' => '', 'og' => 'en_US'],
        'fr' => ['name' => 'Français', 'short' => 'FR', 'prefix' => 'fr', 'og' => 'fr_FR'],
    ];

    /** Paths that are never localized (static assets and framework endpoints). */
    private const UNLOCALIZED = ['css', 'js', 'images', 'build', 'storage', 'favicon.ico', 'robots.txt', 'up'];

    public static function current(): string
    {
        return array_key_exists(App::getLocale(), self::LOCALES) ? App::getLocale() : self::DEFAULT;
    }

    /** Path for the given locale, e.g. url('/contact', 'fr') → '/fr/contact'. */
    public static function path(string $path, ?string $locale = null): string
    {
        $locale ??= self::current();
        $path = '/'.ltrim(self::stripPrefix($path), '/');
        $prefix = self::LOCALES[$locale]['prefix'] ?? '';

        if ($prefix === '' || self::isUnlocalized($path)) {
            return $path;
        }

        return $path === '/' ? '/'.$prefix : '/'.$prefix.$path;
    }

    /** The same page in another locale, keeping the query string. */
    public static function switchUrl(string $locale): string
    {
        $request = request();
        $path = self::path('/'.ltrim($request->path(), '/'), $locale);
        $query = $request->getQueryString();

        return $path.($query ? '?'.$query : '');
    }

    /** Removes a leading locale prefix (/fr, /fr/…) from a path. */
    public static function stripPrefix(string $path): string
    {
        foreach (self::LOCALES as $config) {
            $prefix = $config['prefix'];
            if ($prefix !== '' && ($path === '/'.$prefix || str_starts_with($path, '/'.$prefix.'/'))) {
                return substr($path, strlen($prefix) + 1) ?: '/';
            }
        }

        return $path;
    }

    public static function isUnlocalized(string $path): bool
    {
        $first = explode('/', ltrim($path, '/'))[0];

        return in_array($first, self::UNLOCALIZED, true);
    }

    /**
     * Points internal links (href/action) in rendered HTML at the current locale,
     * so French pages keep visitors in French without changing every template link.
     */
    public static function localizeLinks(string $html, string $locale): string
    {
        $prefix = self::LOCALES[$locale]['prefix'] ?? '';
        if ($prefix === '') {
            return $html;
        }

        $base = rtrim(url('/'), '/');

        $rewrite = function (array $m) use ($base) {
            $value = $m[2];
            $absolute = $base !== '' && str_starts_with($value, $base) && ($value === $base || in_array($value[strlen($base)] ?? '', ['/', '?', '#'], true));

            if (! $absolute && ! (str_starts_with($value, '/') && ! str_starts_with($value, '//'))) {
                return $m[0];
            }

            $relative = $absolute ? (substr($value, strlen($base)) ?: '/') : $value;
            $path = parse_url($relative, PHP_URL_PATH) ?? '/';
            $rest = substr($relative, strlen($path));

            if (self::stripPrefix($path) !== $path || self::isUnlocalized($path)) {
                return $m[0];
            }

            return $m[1].'="'.($absolute ? $base : '').self::path($path).$rest.'"';
        };

        // Only links and forms; tags marked data-no-localize (the language switcher) are left alone.
        return preg_replace_callback(
            '/<(a|form)\b[^>]*>/i',
            fn (array $tag) => str_contains($tag[0], 'data-no-localize')
                ? $tag[0]
                : preg_replace_callback('/\b(href|action)="([^"]*)"/i', $rewrite, $tag[0]),
            $html
        );
    }
}
