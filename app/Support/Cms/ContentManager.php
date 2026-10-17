<?php

namespace App\Support\Cms;

use App\Models\CmsContent;
use App\Models\CmsEntry;
use App\Models\CmsRevision;
use App\Models\User;
use App\Support\Localization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Draft / publish workflow and housekeeping for managed content.
 */
class ContentManager
{
    /** How many published versions are kept per entry and language. */
    private const KEEP_REVISIONS = 20;

    /**
     * Content shown in the editor. A language that has never been written starts from the
     * English structure with empty text (language-independent values are kept).
     */
    public static function working(CmsEntry $entry, string $locale): array
    {
        $content = $entry->contentFor($locale)->working();
        if (is_array($content)) {
            return $content;
        }

        $english = $entry->contentFor(Localization::DEFAULT)->working() ?? [];

        return Tree::copyShared(EditorSchema::blank($english), $english, Registry::sharedPaths($entry->key));
    }

    public static function schema(CmsEntry $entry, string $locale): EditorSchema
    {
        $sources = [];
        foreach ($entry->contents as $content) {
            $sources[] = $content->draft;
            $sources[] = $content->published;
        }
        $sources[] = Registry::defaults()[$entry->key]['content'][Localization::DEFAULT] ?? null;

        // Services and articles share a structure: let each one offer the blocks the others use.
        if (Registry::isCollection($entry)) {
            CmsEntry::query()->where('type', $entry->type)->whereKeyNot($entry->id)->with('contents')->get()
                ->each(function (CmsEntry $sibling) use (&$sources) {
                    $sources[] = $sibling->contentFor(Localization::DEFAULT)->working();
                });
        }

        return new EditorSchema($entry, $locale, $sources);
    }

    public static function saveDraft(CmsEntry $entry, string $locale, array $content, ?User $user): CmsContent
    {
        $row = $entry->contentFor($locale);
        $row->fill([
            'cms_entry_id' => $entry->id,
            'locale' => $locale,
            'draft' => $content,
            'draft_updated_at' => now(),
            'updated_by' => $user?->id,
        ])->save();
        $entry->touch();

        return $row;
    }

    public static function publish(CmsEntry $entry, string $locale, ?User $user): bool
    {
        $row = $entry->contentFor($locale);
        if (! $row->exists || $row->draft === null) {
            return false;
        }

        DB::transaction(function () use ($entry, $locale, $row, $user) {
            $row->fill([
                'published' => $row->draft,
                'draft' => null,
                'published_at' => now(),
                'updated_by' => $user?->id,
            ])->save();

            CmsRevision::create(['cms_entry_id' => $entry->id, 'locale' => $locale, 'content' => $row->published, 'user_id' => $user?->id]);

            // Keep the history short.
            $old = CmsRevision::query()->where('cms_entry_id', $entry->id)->where('locale', $locale)
                ->latest('id')->skip(self::KEEP_REVISIONS)->take(PHP_INT_MAX)->pluck('id');
            CmsRevision::query()->whereIn('id', $old)->delete();

            $entry->touch();
        });

        SiteContent::flush();

        return true;
    }

    public static function discard(CmsEntry $entry, string $locale): void
    {
        $row = $entry->contentFor($locale);
        if ($row->exists) {
            if ($row->published === null) {
                $row->delete();
            } else {
                $row->fill(['draft' => null, 'draft_updated_at' => null])->save();
            }
        }
    }

    /** Takes a published service or article offline in one language (the content becomes a draft). */
    public static function unpublish(CmsEntry $entry, string $locale, ?User $user): void
    {
        $row = $entry->contentFor($locale);
        if ($row->exists && $row->published !== null) {
            $row->fill([
                'draft' => $row->draft ?? $row->published,
                'published' => null,
                'published_at' => null,
                'draft_updated_at' => now(),
                'updated_by' => $user?->id,
            ])->save();
            SiteContent::flush();
        }
    }

    public static function restoreRevision(CmsEntry $entry, CmsRevision $revision, ?User $user): void
    {
        self::saveDraft($entry, $revision->locale, $revision->content, $user);
    }

    /**
     * Translation gaps between English and another language.
     * Returns ['missing' => int, 'paths' => [...], 'unpublished' => bool].
     */
    public static function translationGaps(CmsEntry $entry, string $locale = 'fr'): array
    {
        if (! Registry::isTranslatable($entry->key)) {
            return ['missing' => 0, 'paths' => [], 'unpublished' => false, 'structure' => false];
        }

        $english = $entry->contentFor(Localization::DEFAULT)->working() ?? [];
        $other = $entry->contentFor($locale);
        $translated = $other->working();
        $shared = Registry::sharedPaths($entry->key);

        $paths = [];
        $translatedStrings = is_array($translated) ? Tree::strings($translated) : [];
        foreach (Tree::strings($english) as $path => $value) {
            if (trim($value) === '' || Tree::matches($path, $shared) || str_ends_with($path, '.href') || str_ends_with($path, '.type') || str_ends_with($path, '.icon') || str_ends_with($path, '.style')) {
                continue;
            }
            if (trim($translatedStrings[$path] ?? '') === '') {
                $paths[] = $path;
            }
        }

        return [
            'missing' => count($paths),
            'paths' => $paths,
            'unpublished' => ($entry->contentFor(Localization::DEFAULT)->published !== null) && $other->published === null,
            'structure' => is_array($translated) && self::shape($english) !== self::shape($translated),
        ];
    }

    /** Structural fingerprint (keys and list lengths), to spot items added in one language only. */
    private static function shape(mixed $value): mixed
    {
        if (! is_array($value)) {
            return null;
        }

        return array_map(fn ($item) => self::shape($item), $value);
    }

    /**
     * Other content that links to this service or article (prevents broken links on deletion).
     * Returns a list of entry labels.
     */
    public static function references(CmsEntry $entry): array
    {
        $path = Registry::publicPath($entry);
        if ($path === null) {
            return [];
        }

        $found = [];
        $pattern = '#(?<![\w/-])'.preg_quote($path, '#').'(?![\w-])#';

        CmsEntry::query()->whereKeyNot($entry->id)->with('contents')->get()->each(function (CmsEntry $other) use ($pattern, $entry, &$found) {
            foreach ($other->contents as $content) {
                foreach ([$content->published, $content->draft] as $version) {
                    if (is_array($version) && preg_match($pattern, json_encode($version, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))) {
                        $found[$other->id] = Registry::label($other, $other->contentFor(Localization::DEFAULT)->working());
                    }
                }
            }

            // Quote-form shortcuts point at service slugs.
            if ($entry->type === 'service' && $other->key === 'settings:forms') {
                $aliases = $other->contentFor(Localization::DEFAULT)->working()['quote_form']['service_aliases'] ?? [];
                if (array_key_exists($entry->slug, $aliases)) {
                    $found[$other->id] = Registry::label($other).' (quote form shortcut)';
                }
            }
        });

        return array_values($found);
    }

    /**
     * Imports the original content from the config files.
     * Existing entries are left untouched unless $overwrite is true.
     */
    public static function import(bool $overwrite = false): array
    {
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($overwrite, &$created, &$updated) {
            foreach (Registry::defaults() as $key => $definition) {
                $entry = CmsEntry::withTrashed()->firstWhere('key', $key);
                $isNew = $entry === null;

                if ($entry && ! $overwrite) {
                    continue;
                }

                $entry ??= new CmsEntry(['key' => $key]);
                $entry->fill([
                    'type' => $definition['type'],
                    'slug' => $definition['slug'],
                    'sort_order' => $definition['sort_order'],
                    'is_required' => $definition['required'],
                ])->save();
                if ($entry->trashed()) {
                    $entry->restore();
                }

                foreach ($definition['content'] as $locale => $content) {
                    CmsContent::query()->updateOrCreate(
                        ['cms_entry_id' => $entry->id, 'locale' => $locale],
                        ['published' => $content, 'draft' => null, 'published_at' => now(), 'draft_updated_at' => null],
                    );
                }

                $isNew ? $created++ : $updated++;
            }
        });

        SiteContent::flush();

        return compact('created', 'updated');
    }

    /** Entries for the dashboard sidebar and lists, with their content loaded. */
    public static function entries(?string $type = null): Collection
    {
        return CmsEntry::query()
            ->when($type, fn ($query) => $query->where('type', $type))
            ->with('contents')
            ->orderBy('sort_order')->orderBy('id')
            ->get();
    }
}
