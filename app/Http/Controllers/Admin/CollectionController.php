<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsEntry;
use App\Support\Cms\ContentManager;
use App\Support\Cms\EditorSchema;
use App\Support\Cms\Registry;
use App\Support\Cms\SiteContent;
use App\Support\Localization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Services and Knowledge Center articles: list, add, reorder.
 */
class CollectionController extends Controller
{
    public function index(Request $request, string $type): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', ''),
            'lang' => in_array($request->query('lang'), Registry::locales(), true) ? $request->query('lang') : '',
        ];

        $query = CmsEntry::query()->where('type', $type)->with('contents')->orderBy('sort_order')->orderBy('id');
        if ($filters['status'] === 'deleted') {
            $query->onlyTrashed();
        }

        $languages = $filters['lang'] ? [$filters['lang']] : Registry::locales();

        $items = $query->get()->map(function (CmsEntry $entry) use ($languages) {
            $english = $entry->contentFor(Localization::DEFAULT);
            $states = [];
            foreach (Registry::locales() as $locale) {
                $row = $entry->contentFor($locale);
                $states[$locale] = match (true) {
                    ! $row->exists => 'missing',
                    $row->published === null => 'unpublished',
                    $row->draft !== null => 'changes',
                    default => 'published',
                };
            }

            return [
                'entry' => $entry,
                'title' => Registry::label($entry, $english->working()),
                'summary' => $english->working()['category'] ?? ($english->working()['eyebrow'] ?? null),
                'states' => $states,
                'filtered_states' => array_intersect_key($states, array_flip($languages)),
                'gaps' => ContentManager::translationGaps($entry),
                'linked' => ! $entry->trashed() && ContentManager::references($entry) !== [],
                'updated_at' => $entry->updated_at,
            ];
        });

        $items = $items->filter(function ($item) use ($filters) {
            if ($filters['q'] !== '' && ! str_contains(mb_strtolower($item['title'].' '.$item['entry']->slug), mb_strtolower($filters['q']))) {
                return false;
            }

            return match ($filters['status']) {
                'published' => in_array('published', $item['filtered_states'], true) || in_array('changes', $item['filtered_states'], true),
                'draft' => (bool) array_intersect($item['filtered_states'], ['changes', 'unpublished', 'missing']),
                default => true,
            };
        })->values();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.collections.index', [
            'type' => $type,
            'meta' => Registry::COLLECTIONS[$type],
            'items' => $paginator,
            'filters' => $filters,
            'canReorder' => $filters['q'] === '' && $filters['status'] === '' && $items->count() <= $perPage,
            'trashedCount' => CmsEntry::onlyTrashed()->where('type', $type)->count(),
        ]);
    }

    public function create(string $type): View
    {
        return view('admin.collections.create', [
            'type' => $type,
            'meta' => Registry::COLLECTIONS[$type],
            'sources' => ContentManager::entries($type)->mapWithKeys(fn (CmsEntry $entry) => [
                $entry->id => Registry::label($entry, $entry->contentFor(Localization::DEFAULT)->working()),
            ]),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'source' => ['required', Rule::exists('cms_entries', 'id')->where('type', $type)->whereNull('deleted_at')],
            'copy_text' => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'Use lowercase letters, numbers and hyphens only (for example: voice-over-services).',
            'source.required' => 'Choose which existing '.Registry::COLLECTIONS[$type]['singular'].' to start from.',
        ]);

        $key = "{$type}:{$data['slug']}";
        if (CmsEntry::withTrashed()->where('key', $key)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'This address is already used (check the deleted items too).']);
        }

        $source = CmsEntry::query()->with('contents')->findOrFail($data['source']);

        $entry = DB::transaction(function () use ($type, $data, $key, $source, $request) {
            $entry = CmsEntry::create([
                'key' => $key,
                'type' => $type,
                'slug' => $data['slug'],
                'sort_order' => (int) CmsEntry::query()->where('type', $type)->max('sort_order') + 1,
                'is_required' => false,
            ]);

            // New items start as unpublished drafts, in every language.
            foreach (Registry::locales() as $locale) {
                $content = $source->contentFor($locale)->working();
                if (! is_array($content)) {
                    continue;
                }
                if (! $request->boolean('copy_text')) {
                    $content = EditorSchema::blank($content);
                    // Keep the layout switch of the source (e.g. the localization layout).
                    foreach (Registry::HIDDEN_KEYS as $hidden) {
                        if (array_key_exists($hidden, $source->contentFor($locale)->working() ?? [])) {
                            $content[$hidden] = $source->contentFor($locale)->working()[$hidden];
                        }
                    }
                }
                if ($locale === Localization::DEFAULT && array_key_exists('title', $content)) {
                    $content['title'] = $data['title'];
                }
                ContentManager::saveDraft($entry, $locale, $content, $request->user());
            }

            return $entry;
        });

        return redirect()->route('admin.entries.edit', $entry)
            ->with('success', 'Created as a draft. Fill in the content, preview it, then publish each language.'
                .($type === 'service' ? ' Then link to it from the Services overview or the main navigation so visitors can find it.' : ''));
    }

    public function reorder(Request $request, string $type): JsonResponse|RedirectResponse
    {
        $ids = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ])['order'];

        $valid = CmsEntry::query()->where('type', $type)->whereIn('id', $ids)->pluck('id')->all();
        DB::transaction(function () use ($ids, $valid) {
            foreach (array_values(array_intersect($ids, $valid)) as $position => $id) {
                CmsEntry::query()->whereKey($id)->update(['sort_order' => $position]);
            }
        });
        SiteContent::flush();

        return $request->expectsJson()
            ? response()->json(['message' => 'Order saved.'])
            : back()->with('success', 'Order saved.');
    }
}
