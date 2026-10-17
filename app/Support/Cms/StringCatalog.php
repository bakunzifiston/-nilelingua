<?php

namespace App\Support\Cms;

use App\Models\CmsString;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * The interface text written in the templates (headings, buttons, labels), grouped by page.
 */
class StringCatalog
{
    /** Template file (relative to resources/views, without extension) => area shown in the dashboard. */
    private const AREAS = [
        'site/home' => 'Home',
        'site/pages/about' => 'About',
        'site/pages/services' => 'Services overview',
        'site/pages/service' => 'Service pages',
        'site/pages/localization' => 'Service pages',
        'site/pages/industries' => 'Industries',
        'site/pages/languages' => 'Languages',
        'site/pages/technology' => 'Technology',
        'site/pages/contact' => 'Contact',
        'site/pages/careers' => 'Careers',
        'site/pages/knowledge-center' => 'Knowledge Center',
        'site/pages/article' => 'Knowledge Center articles',
        'site/pages/faq' => 'FAQ',
        'site/pages/privacy-policy' => 'Privacy policy',
        'site/thank-you' => 'Thank-you page',
        'partials' => 'Header & footer',
        'layouts' => 'Header & footer',
        'components' => 'Shared elements',
        'site/partials' => 'Shared elements',
        'site/pages/generic' => 'Shared elements',
        'routes' => 'Form messages',
    ];

    public const OTHER_AREA = 'Form options & other';

    /**
     * Every string: [key, areas[], en (edited or null), fr (current French), fr_edited (bool), missing_fr (bool), id].
     */
    public static function all(): Collection
    {
        $sources = Cache::remember('cms.string-sources', 300, fn () => self::scan());
        $french = self::frenchFile();

        $keys = array_keys($sources + array_fill_keys(array_keys($french), [self::OTHER_AREA]));
        $edited = CmsString::query()->get()->keyBy('key');

        return collect($keys)->map(function (string $key) use ($sources, $french, $edited) {
            $row = $edited->get($key);
            $frenchValue = ($row?->fr !== null && $row->fr !== '') ? $row->fr : ($french[$key] ?? null);

            return [
                'key' => $key,
                'hash' => CmsString::hashFor($key),
                'areas' => $sources[$key] ?? [self::OTHER_AREA],
                'en' => $row?->en,
                'fr' => $frenchValue,
                'en_edited' => $row?->en !== null && $row?->en !== '',
                'fr_edited' => $row?->fr !== null && $row?->fr !== '',
                'missing_fr' => $frenchValue === null || $frenchValue === '',
                'has_html' => (bool) preg_match('/<[a-z]/i', $key),
                'updated_at' => $row?->updated_at,
            ];
        })->sortBy(fn ($row) => [$row['areas'][0], mb_strtolower($row['key'])])->values();
    }

    public static function areas(): array
    {
        return array_values(array_unique([...array_values(self::AREAS), self::OTHER_AREA]));
    }

    public static function forget(): void
    {
        Cache::forget('cms.string-sources');
    }

    /** French translations shipped in lang/fr.json. */
    public static function frenchFile(): array
    {
        $path = lang_path('fr.json');

        return is_file($path) ? (json_decode(File::get($path), true) ?: []) : [];
    }

    /** Finds __('…') calls in the templates and routes: [key => areas[]]. */
    private static function scan(): array
    {
        $files = collect(File::allFiles(resource_path('views')))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php') && ! str_starts_with($file->getRelativePathname(), 'admin'))
            ->mapWithKeys(fn ($file) => [str_replace('.blade.php', '', $file->getRelativePathname()) => $file->getPathname()])
            ->put('routes', base_path('routes/web.php'));

        $found = [];
        foreach ($files as $relative => $path) {
            $area = self::areaFor($relative);
            preg_match_all("/__\\(\\s*'((?:[^'\\\\]|\\\\.)*)'/", File::get($path), $matches);
            foreach ($matches[1] as $raw) {
                $key = stripslashes($raw);
                $found[$key] ??= [];
                if (! in_array($area, $found[$key], true)) {
                    $found[$key][] = $area;
                }
            }
        }

        return $found;
    }

    private static function areaFor(string $relative): string
    {
        if (str_starts_with($relative, 'site/home')) {
            return 'Home';
        }

        $candidates = array_filter(array_keys(self::AREAS), fn ($prefix) => $relative === $prefix || str_starts_with($relative, $prefix.'/'));
        usort($candidates, fn ($a, $b) => strlen($b) <=> strlen($a));

        return $candidates ? self::AREAS[$candidates[0]] : 'Shared elements';
    }
}
