<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsString;
use App\Support\Cms\DatabaseTranslationLoader;
use App\Support\Cms\Markup;
use App\Support\Cms\StringCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Headings, buttons and labels that are part of the page templates.
 */
class StringController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'area' => (string) $request->query('area', ''),
            'status' => (string) $request->query('status', ''),
        ];

        $strings = StringCatalog::all()->filter(function ($row) use ($filters) {
            if ($filters['area'] !== '' && ! in_array($filters['area'], $row['areas'], true)) {
                return false;
            }
            if ($filters['q'] !== '') {
                $haystack = mb_strtolower($row['key'].' '.$row['en'].' '.$row['fr']);
                if (! str_contains($haystack, mb_strtolower($filters['q']))) {
                    return false;
                }
            }

            return match ($filters['status']) {
                'missing' => $row['missing_fr'],
                'edited' => $row['en_edited'] || $row['fr_edited'],
                default => true,
            };
        })->values();

        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return view('admin.strings.index', [
            'strings' => new LengthAwarePaginator($strings->forPage($page, $perPage)->values(), $strings->count(), $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]),
            'filters' => $filters,
            'areas' => StringCatalog::areas(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'strings' => ['required', 'array', 'max:100'],
            'strings.*.key' => ['required', 'string'],
            'strings.*.en' => ['nullable', 'string', 'max:5000'],
            'strings.*.fr' => ['nullable', 'string', 'max:5000'],
        ]);

        $known = StringCatalog::all()->keyBy('hash');
        $french = StringCatalog::frenchFile();
        $changed = 0;

        foreach ($data['strings'] as $hash => $values) {
            $row = $known->get($hash);
            if ($row === null || $row['key'] !== $values['key']) {
                continue;
            }

            // Store only real changes: an English value equal to the template text, or a French
            // value equal to the shipped translation, is not an override.
            $en = Markup::sanitizeInterfaceHtml(trim((string) ($values['en'] ?? '')));
            $fr = Markup::sanitizeInterfaceHtml(trim((string) ($values['fr'] ?? '')));
            $en = ($en === '' || $en === $row['key']) ? null : $en;
            $fr = ($fr === '' || $fr === ($french[$row['key']] ?? null)) ? null : $fr;

            $record = CmsString::query()->firstWhere('key_hash', $hash);
            if ($en === null && $fr === null) {
                if ($record) {
                    $record->delete();
                    $changed++;
                }

                continue;
            }

            $record ??= new CmsString(['key_hash' => $hash, 'key' => $row['key']]);
            $record->fill(['en' => $en, 'fr' => $fr]);
            if ($record->isDirty()) {
                $record->updated_by = $request->user()->id;
                $record->save();
                $changed++;
            }
        }

        DatabaseTranslationLoader::flush();

        return back()->with('success', $changed ? "Saved {$changed} ".str('change')->plural($changed).'. The website shows them immediately.' : 'No changes to save.');
    }
}
