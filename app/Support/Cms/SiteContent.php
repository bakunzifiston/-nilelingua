<?php

namespace App\Support\Cms;

use App\Models\CmsEntry;
use App\Support\Localization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Builds the site content tree (what templates read as config('site')) for a language.
 *
 * The database is the source of truth once content has been imported; the config files
 * (config/site.php, config/site_fr.php) only provide the original content used for the
 * first import and as a safety net when the CMS tables do not exist yet.
 */
class SiteContent
{
    private const CACHE_PREFIX = 'cms.site.';

    private static array $base = [];

    /** Original content from the configuration files. */
    public static function baseConfig(string $locale): array
    {
        if (! isset(self::$base[$locale])) {
            $file = $locale === Localization::DEFAULT ? 'site.php' : "site_{$locale}.php";
            $path = config_path($file);
            self::$base[$locale] = is_file($path) ? require $path : (require config_path('site.php'));
        }

        return self::$base[$locale];
    }

    /** Published content for visitors (cached until the next publish). */
    public static function published(string $locale): array
    {
        try {
            return Cache::rememberForever(self::CACHE_PREFIX.$locale, fn () => self::build($locale));
        } catch (Throwable $e) {
            report($e);

            return self::baseConfig($locale);
        }
    }

    /**
     * Content as it would look with a draft applied (admin preview only).
     */
    public static function preview(string $locale, CmsEntry $entry, array $content): array
    {
        return self::build($locale, [$entry->id => $content]);
    }

    public static function flush(): void
    {
        foreach (Registry::locales() as $locale) {
            Cache::forget(self::CACHE_PREFIX.$locale);
        }
    }

    /**
     * @param  array<int, array>  $overrides  entry id => content to use instead of the published version
     */
    private static function build(string $locale, array $overrides = []): array
    {
        $site = self::baseConfig($locale);

        if (! self::tablesExist()) {
            return $site;
        }

        $entries = CmsEntry::query()
            ->with(['contents' => fn ($query) => $query->whereIn('locale', array_unique([$locale, Localization::DEFAULT]))])
            ->orderBy('type')->orderBy('sort_order')->orderBy('id')
            ->get();

        if ($entries->isEmpty()) {
            return $site;
        }

        // Collections are rebuilt from the database so removed or unpublished items disappear.
        $site['services'] = [];
        $site['articles'] = [];

        foreach ($entries as $entry) {
            $contentLocale = Registry::isTranslatable($entry->key) ? $locale : Localization::DEFAULT;
            $content = $overrides[$entry->id] ?? $entry->contentFor($contentLocale)->published;

            // Never published in this language: core pages keep their original content,
            // services and articles stay hidden.
            if ($content === null) {
                continue;
            }

            // Language-independent values (photos, phone numbers…) always come from English.
            if ($contentLocale !== Localization::DEFAULT && ($shared = Registry::sharedPaths($entry->key))) {
                $english = $entry->contentFor(Localization::DEFAULT)->published;
                if (is_array($english)) {
                    $content = Tree::copyShared($content, $english, $shared);
                }
            }

            Registry::apply($site, $entry->type, $entry->slug, $content);
        }

        return $site;
    }

    private static ?bool $tables = null;

    private static function tablesExist(): bool
    {
        try {
            return self::$tables ??= Schema::hasTable('cms_entries') && Schema::hasTable('cms_contents');
        } catch (Throwable) {
            return false;
        }
    }
}
