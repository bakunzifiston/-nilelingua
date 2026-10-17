<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use App\Models\CmsEntry;
use App\Models\CmsRevision;
use App\Models\LinguistApplication;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\ProjectEnquiry;
use App\Support\Cms\ContentManager;
use App\Support\Cms\Registry;
use App\Support\Cms\StringCatalog;
use App\Support\Localization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $entries = ContentManager::entries();
        $english = fn (CmsEntry $entry) => $entry->contentFor(Localization::DEFAULT);
        $label = fn (CmsEntry $entry) => Registry::label($entry, $english($entry)->working());
        $hasDraft = fn (CmsEntry $entry) => $entry->contents->contains(fn ($content) => $content->draft !== null);

        $count = fn (string ...$types) => [
            'total' => $entries->whereIn('type', $types)->count(),
            'published' => $entries->whereIn('type', $types)->filter(fn ($entry) => $english($entry)->published !== null)->count(),
        ];

        // Everything that needs an action: unpublished drafts first, then French gaps.
        $attention = collect();
        CmsContent::query()->whereNotNull('draft')->whereHas('entry')->with(['entry', 'editor'])->latest('draft_updated_at')->get()
            ->each(function (CmsContent $row) use ($attention, $label) {
                $attention->push([
                    'kind' => 'draft',
                    'title' => $label($row->entry),
                    'locale' => $row->locale,
                    'detail' => 'Unpublished changes · saved '.$row->draft_updated_at?->diffForHumans().($row->editor ? ' by '.$row->editor->name : ''),
                    'url' => route('admin.entries.edit', [$row->entry, 'lang' => $row->locale]),
                    'action' => 'Review',
                ]);
            });
        $entries->each(function (CmsEntry $entry) use ($attention, $label) {
            $gaps = ContentManager::translationGaps($entry);
            if ($gaps['missing'] === 0 && ! $gaps['unpublished']) {
                return;
            }
            $attention->push([
                'kind' => 'translation',
                'title' => $label($entry),
                'locale' => 'fr',
                'detail' => $gaps['unpublished'] ? 'French version not published' : $gaps['missing'].' French '.($gaps['missing'] === 1 ? 'field is' : 'fields are').' empty',
                'url' => route('admin.entries.edit', [$entry, 'lang' => 'fr']),
                'action' => 'Translate',
            ]);
        });
        $missingStrings = StringCatalog::all()->where('missing_fr', true)->count();
        if ($missingStrings) {
            $attention->push([
                'kind' => 'translation',
                'title' => 'Interface text',
                'locale' => 'fr',
                'detail' => $missingStrings.' '.($missingStrings === 1 ? 'label has' : 'labels have').' no French translation',
                'url' => route('admin.strings.index', ['status' => 'missing']),
                'action' => 'Translate',
            ]);
        }

        // Latest activity, one row per page (with the languages that changed).
        $recent = CmsContent::query()->whereHas('entry')->with(['entry', 'editor'])->latest('updated_at')->take(40)->get()
            ->groupBy('cms_entry_id')->take(6)
            ->map(fn ($rows) => [
                'entry' => $rows->first()->entry,
                'title' => $label($rows->first()->entry),
                'locales' => $rows->pluck('locale')->unique()->sort()->values()->all(),
                'when' => $rows->first()->updated_at,
                'editor' => $rows->first()->editor?->name,
                'draft' => $rows->contains(fn ($row) => $row->draft !== null),
            ])->values();

        $pages = $entries->whereIn('type', ['home', 'page'])
            ->sortBy(fn (CmsEntry $entry) => [$entry->type === 'home' ? 0 : 1, $entry->sort_order])
            ->map(fn (CmsEntry $entry) => ['entry' => $entry, 'title' => $label($entry), 'draft' => $hasDraft($entry), 'home' => $entry->type === 'home'])
            ->values();

        return view('admin.dashboard', $this->charts($entries) + [
            'stats' => [
                'pages' => $count('home', 'page'),
                'services' => $count('service'),
                'articles' => $count('article'),
                'media' => Media::query()->count(),
            ],
            'draftCount' => $attention->where('kind', 'draft')->count(),
            'attention' => $attention,
            'recent' => $recent,
            'pages' => $pages,
            'general' => $entries->firstWhere('key', 'settings:general'),
        ]);
    }

    /** Chart data, all read from the database. */
    private function charts($entries): array
    {
        $palette = ['#0F5C52', '#E0A84F', '#2F4F86', '#A44E31', '#49B3A0', '#8A6FB0', '#9AA7AA'];

        // Website submissions per month (last 6 months).
        $months = collect(range(5, 0))->map(fn ($back) => now()->startOfMonth()->subMonths($back));
        $perMonth = function (string $model) use ($months) {
            $counts = $model::query()->where('created_at', '>=', $months->first())->pluck('created_at')
                ->countBy(fn ($date) => Carbon::parse($date)->format('Y-m'));

            return $months->map(fn ($month) => $counts[$month->format('Y-m')] ?? 0)->all();
        };
        $submissions = [
            ['name' => 'Quote requests', 'color' => $palette[0], 'values' => $perMonth(ProjectEnquiry::class)],
            ['name' => 'Linguist applications', 'color' => $palette[1], 'values' => $perMonth(LinguistApplication::class)],
            ['name' => 'Newsletter sign-ups', 'color' => $palette[2], 'values' => $perMonth(NewsletterSubscriber::class)],
        ];

        // Quote requests by service (all time).
        $byService = ProjectEnquiry::query()->selectRaw('service, count(*) as total')->groupBy('service')->orderByDesc('total')->pluck('total', 'service');
        $services = $byService->values()->map(fn ($total, $index) => [
            'label' => $byService->keys()[$index],
            'value' => (int) $total,
            'color' => $palette[$index % count($palette)],
            'href' => route('admin.submissions.index', ['quotes', 'option' => $byService->keys()[$index]]),
        ])->all();

        // Publication status of every page, service and article in each language.
        $status = ['live' => 0, 'changes' => 0, 'unpublished' => 0];
        foreach ($entries as $entry) {
            foreach (Registry::localesFor($entry->key) as $locale) {
                $row = $entry->contentFor($locale);
                $status[match (true) {
                    $row->published === null => 'unpublished',
                    $row->draft !== null => 'changes',
                    default => 'live',
                }]++;
            }
        }

        // Publishing activity per week (last 8 weeks).
        $weeks = collect(range(7, 0))->map(fn ($back) => now()->startOfWeek()->subWeeks($back));
        $published = CmsRevision::query()->where('created_at', '>=', $weeks->first())->get(['created_at', 'locale'])
            ->groupBy(fn ($revision) => $revision->locale.'|'.$revision->created_at->copy()->startOfWeek()->format('Y-m-d'))
            ->map->count();
        $weekly = fn (string $locale) => $weeks->map(fn ($week) => $published[$locale.'|'.$week->format('Y-m-d')] ?? 0)->all();

        return [
            'chartSubmissions' => [
                'categories' => $months->map(fn ($month) => $month->format('M'))->all(),
                'series' => $submissions,
                'total' => collect($submissions)->sum(fn ($line) => array_sum($line['values'])),
            ],
            'chartServices' => $services,
            'chartStatus' => [
                ['label' => 'Published', 'value' => $status['live'], 'color' => '#2E8B57'],
                ['label' => 'Unpublished changes', 'value' => $status['changes'], 'color' => $palette[1]],
                ['label' => 'Not published', 'value' => $status['unpublished'], 'color' => '#9AA7AA'],
            ],
            'chartActivity' => [
                'categories' => $weeks->map(fn ($week) => $week->format('j M'))->all(),
                'series' => [
                    ['name' => 'English', 'color' => $palette[0], 'values' => $weekly('en')],
                    ['name' => 'French', 'color' => $palette[2], 'values' => $weekly('fr')],
                ],
                'total' => $published->sum(),
            ],
        ];
    }
}
