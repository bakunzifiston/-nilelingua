<?php

namespace App\Support\Cms;

use App\Models\CmsString;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Adds interface text edited in the dashboard on top of the lang/*.json files.
 */
class DatabaseTranslationLoader implements Loader
{
    private const CACHE_PREFIX = 'cms.strings.';

    public function __construct(private Loader $files) {}

    public function load($locale, $group, $namespace = null)
    {
        $lines = $this->files->load($locale, $group, $namespace);

        if ($group === '*' && $namespace === '*') {
            $lines = array_replace($lines, self::overrides($locale));
        }

        return $lines;
    }

    /** Edited strings for a language: [English source => text]. */
    public static function overrides(string $locale): array
    {
        if (! in_array($locale, Registry::locales(), true)) {
            return [];
        }

        try {
            return Cache::rememberForever(self::CACHE_PREFIX.$locale, function () use ($locale) {
                if (! Schema::hasTable('cms_strings')) {
                    return [];
                }

                return CmsString::query()->whereNotNull($locale)->where($locale, '!=', '')
                    ->pluck($locale, 'key')->all();
            });
        } catch (Throwable) {
            return [];
        }
    }

    public static function flush(): void
    {
        foreach (Registry::locales() as $locale) {
            Cache::forget(self::CACHE_PREFIX.$locale);
        }
    }

    public function addNamespace($namespace, $hint)
    {
        $this->files->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->files->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->files->namespaces();
    }
}
