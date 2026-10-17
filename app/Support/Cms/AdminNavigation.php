<?php

namespace App\Support\Cms;

use App\Http\Controllers\Admin\SubmissionController;
use App\Models\CmsEntry;
use Illuminate\Http\Request;

/**
 * Sidebar groups for the admin dashboard.
 */
class AdminNavigation
{
    private static function submissions(?string $route, string $type): array
    {
        $counts = SubmissionController::counts();
        $items = [];
        foreach (['quotes' => ['Quote requests', 'form'], 'applications' => ['Applications', 'users'], 'subscribers' => ['Newsletter', 'article']] as $key => [$label, $icon]) {
            $items[] = [
                'label' => $label,
                'url' => route('admin.submissions.index', $key),
                'icon' => $icon,
                'active' => str_starts_with((string) $route, 'admin.submissions.') && $type === $key,
                'count' => $key === 'subscribers' ? $counts[$key]['total'] : ($counts[$key]['new'] ?: null),
                'new' => $key !== 'subscribers' && $counts[$key]['new'] > 0,
            ];
        }

        return $items;
    }

    public static function build(Request $request): array
    {
        $entries = ContentManager::entries();
        $current = $request->route('entry');
        $currentId = $current instanceof CmsEntry ? $current->id : null;
        $currentType = $current instanceof CmsEntry ? $current->type : $request->route('type');
        $route = $request->route()?->getName();

        $entryItem = fn (CmsEntry $entry, string $icon) => [
            'label' => Registry::label($entry),
            'url' => route('admin.entries.edit', $entry),
            'icon' => $icon,
            'active' => $currentId === $entry->id,
            'draft' => $entry->contents->contains(fn ($content) => $content->draft !== null),
        ];

        $pages = $entries->whereIn('type', ['home', 'page'])
            ->sortBy(fn (CmsEntry $entry) => [$entry->type === 'home' ? 0 : 1, $entry->sort_order])
            ->map(fn (CmsEntry $entry) => $entryItem($entry, $entry->type === 'home' ? 'home' : 'page'))
            ->values()->all();

        $collections = [];
        foreach (Registry::COLLECTIONS as $type => $meta) {
            $collections[] = [
                'label' => $meta['nav'] ?? $meta['label'],
                'url' => route('admin.collections.index', $type),
                'icon' => $type === 'service' ? 'service' : 'article',
                'active' => in_array($route, ['admin.collections.index', 'admin.collections.create'], true) && $request->route('type') === $type
                    || ($currentType === $type && $currentId !== null),
                'count' => $entries->where('type', $type)->count(),
                'draft' => $entries->where('type', $type)->contains(fn ($entry) => $entry->contents->contains(fn ($content) => $content->draft !== null)),
            ];
        }

        $settingsIcons = ['general' => 'settings', 'navigation' => 'nav', 'forms' => 'form'];
        $settings = $entries->where('type', 'settings')
            ->map(fn (CmsEntry $entry) => $entryItem($entry, $settingsIcons[$entry->slug] ?? 'settings'))
            ->values()->all();
        $settings[] = ['label' => 'Interface text', 'url' => route('admin.strings.index'), 'icon' => 'text', 'active' => $route === 'admin.strings.index'];

        return [
            ['title' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'icon' => 'dashboard', 'active' => $route === 'admin.dashboard'],
            ]],
            ['title' => 'Submissions', 'items' => self::submissions($route, (string) $request->route('type'))],
            ['title' => 'Website pages', 'items' => $pages],
            ['title' => 'Collections', 'items' => $collections],
            ['title' => 'Site settings', 'items' => $settings],
            ['title' => 'Library & access', 'items' => [
                ['label' => 'Media library', 'url' => route('admin.media.index'), 'icon' => 'image', 'active' => $route === 'admin.media.index'],
                ['label' => 'Administrators', 'url' => route('admin.users.index'), 'icon' => 'users', 'active' => $route === 'admin.users.index'],
            ]],
        ];
    }
}
