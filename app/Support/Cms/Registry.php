<?php

namespace App\Support\Cms;

use App\Models\CmsEntry;
use App\Support\Localization;

/**
 * Describes every kind of managed content: how it maps onto the site content tree,
 * how it is labelled in the dashboard and which parts are protected or shared.
 */
class Registry
{
    /** Core pages, in sidebar order (slug => dashboard label). */
    public const PAGES = [
        'about' => 'About',
        'services' => 'Services overview',
        'industries' => 'Industries',
        'languages' => 'Languages',
        'technology' => 'Technology',
        'contact' => 'Contact',
        'careers' => 'Careers',
        'knowledge-center' => 'Knowledge Center',
        'faq' => 'FAQ',
        'privacy-policy' => 'Privacy policy',
    ];

    public const SETTINGS = [
        'general' => 'General & contact details',
        'navigation' => 'Main navigation',
        'forms' => 'Form options',
    ];

    /** Collections the administrator can add to, reorder and remove from. */
    public const COLLECTIONS = [
        'service' => ['label' => 'Services', 'singular' => 'service', 'url' => '/services/'],
        'article' => ['label' => 'Knowledge Center articles', 'nav' => 'Articles', 'singular' => 'article', 'url' => '/knowledge-center/'],
    ];

    /** Settings stored once and used for every language (form values must stay identical). */
    public const UNTRANSLATED = ['settings:forms'];

    /**
     * Language-independent values: edited in English and reused for every language.
     * Patterns use "*" for any key.
     */
    public const SHARED = [
        'page:about' => ['founder.photo', 'founder.linkedin'],
        'settings:general' => ['contact_info.maps_url', 'contact_info.phone', 'contact_info.whatsapp_url', 'contact_info.email', 'social.*.url'],
    ];

    /** Keys kept as they are and never shown in the editor (layout switches, internal values). */
    public const HIDDEN_KEYS = ['localization_layout', 'service_aliases'];

    /** Homepage sections that must always exist. */
    public const REQUIRED_HOME_SECTIONS = ['hero', 'services'];

    public static function locales(): array
    {
        return array_keys(Localization::LOCALES);
    }

    public static function isTranslatable(string $key): bool
    {
        return ! in_array($key, self::UNTRANSLATED, true);
    }

    /** Languages an entry is edited in. */
    public static function localesFor(string $key): array
    {
        return self::isTranslatable($key) ? self::locales() : [Localization::DEFAULT];
    }

    public static function sharedPaths(string $key): array
    {
        return self::SHARED[$key] ?? [];
    }

    /** Human label for an entry. */
    public static function label(CmsEntry $entry, ?array $content = null): string
    {
        return match ($entry->type) {
            'home' => 'Home',
            'page' => self::PAGES[$entry->slug] ?? str($entry->slug)->headline()->toString(),
            'settings' => self::SETTINGS[$entry->slug] ?? str($entry->slug)->headline()->toString(),
            default => ($content['title'] ?? null) ?: str($entry->slug)->headline()->toString(),
        };
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'home', 'page' => 'Page',
            'service' => 'Service',
            'article' => 'Article',
            'settings' => 'Settings',
            default => ucfirst($type),
        };
    }

    /** Public path of an entry (without language prefix), or null for settings. */
    public static function publicPath(CmsEntry $entry): ?string
    {
        return match ($entry->type) {
            'home' => '/',
            'page' => '/'.$entry->slug,
            'service' => '/services/'.$entry->slug,
            'article' => '/knowledge-center/'.$entry->slug,
            default => null,
        };
    }

    public static function publicUrl(CmsEntry $entry, string $locale): ?string
    {
        $path = self::publicPath($entry);

        return $path === null ? null : url(Localization::path($path, $locale));
    }

    /** Collections and settings can be unpublished/deleted only where it is safe. */
    public static function isCollection(CmsEntry $entry): bool
    {
        return array_key_exists($entry->type, self::COLLECTIONS);
    }

    /**
     * Content of every entry, read from the site configuration files (the original content).
     * Returns [key => ['type', 'slug', 'sort_order', 'required', 'content' => [locale => array]]].
     */
    public static function defaults(): array
    {
        static $memo = null;
        if ($memo !== null) {
            return $memo;
        }

        $files = [];
        foreach (self::locales() as $locale) {
            $files[$locale] = SiteContent::baseConfig($locale);
        }

        $entries = [];
        $add = function (string $key, string $type, ?string $slug, int $order, bool $required, callable $pick) use (&$entries, $files) {
            $content = [];
            foreach (self::localesFor($key) as $locale) {
                $value = $pick($files[$locale]);
                if ($value !== null) {
                    $content[$locale] = $value;
                }
            }
            $entries[$key] = compact('type', 'slug', 'required', 'content') + ['sort_order' => $order];
        };

        $add('home', 'home', null, 0, true, fn ($site) => $site['home'] ?? null);

        $order = 1;
        foreach (self::PAGES as $slug => $label) {
            $add("page:{$slug}", 'page', $slug, $order++, true, function ($site) use ($slug) {
                $page = $site['pages'][$slug] ?? null;
                if ($page !== null && $slug === 'faq') {
                    $page['groups'] = $site['faq'] ?? [];
                }

                return $page;
            });
        }

        foreach (['service' => 'services', 'article' => 'articles'] as $type => $group) {
            $position = 0;
            foreach (array_keys($files[Localization::DEFAULT][$group] ?? []) as $slug) {
                $add("{$type}:{$slug}", $type, $slug, $position++, false, fn ($site) => $site[$group][$slug] ?? null);
            }
        }

        $add('settings:general', 'settings', 'general', 0, true, fn ($site) => [
            'name' => $site['name'] ?? '',
            'tagline' => $site['tagline'] ?? '',
            'contact_info' => $site['contact_info'] ?? [],
            'social' => $site['social'] ?? [],
        ]);
        $add('settings:navigation', 'settings', 'navigation', 1, true, fn ($site) => ['items' => $site['navigation'] ?? []]);
        $add('settings:forms', 'settings', 'forms', 2, true, fn ($site) => [
            'quote_form' => $site['quote_form'] ?? [],
            'application_form' => $site['application_form'] ?? [],
        ]);

        return $memo = $entries;
    }

    /**
     * Writes one entry's content into the site content tree.
     */
    public static function apply(array &$site, string $type, ?string $slug, array $content): void
    {
        switch ($type) {
            case 'home':
                $site['home'] = $content;
                break;
            case 'page':
                if ($slug === 'faq') {
                    $site['faq'] = $content['groups'] ?? [];
                    unset($content['groups']);
                }
                $site['pages'][$slug] = $content;
                break;
            case 'service':
                $site['services'][$slug] = $content;
                break;
            case 'article':
                $site['articles'][$slug] = $content;
                break;
            case 'settings':
                match ($slug) {
                    'general' => $site = array_replace($site, array_intersect_key($content, array_flip(['name', 'tagline', 'contact_info', 'social']))),
                    'navigation' => $site['navigation'] = $content['items'] ?? [],
                    'forms' => $site = array_replace($site, array_intersect_key($content, array_flip(['quote_form', 'application_form']))),
                    default => null,
                };
                break;
        }
    }
}
