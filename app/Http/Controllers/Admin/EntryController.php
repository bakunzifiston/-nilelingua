<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SiteController;
use App\Models\CmsEntry;
use App\Models\CmsRevision;
use App\Support\Cms\ContentManager;
use App\Support\Cms\FormPayload;
use App\Support\Cms\Registry;
use App\Support\Cms\SiteContent;
use App\Support\Localization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class EntryController extends Controller
{
    public function edit(Request $request, CmsEntry $entry): View
    {
        $locale = $this->locale($request, $entry);
        $entry->load('contents.editor');

        $row = $entry->contentFor($locale);
        $content = session('cms_content_'.$entry->id.'_'.$locale) ?? ContentManager::working($entry, $locale);

        return view('admin.entries.edit', [
            'entry' => $entry,
            'locale' => $locale,
            'locales' => Registry::localesFor($entry->key),
            'row' => $row,
            'content' => $content,
            'reference' => $locale === Localization::DEFAULT ? null : ContentManager::working($entry, Localization::DEFAULT),
            'schema' => ContentManager::schema($entry, $locale),
            'label' => Registry::label($entry, $entry->contentFor(Localization::DEFAULT)->working()),
            'gaps' => ContentManager::translationGaps($entry),
            'revisionCount' => $entry->revisions()->where('locale', $locale)->count(),
            'publicUrl' => Registry::publicUrl($entry, $locale),
        ]);
    }

    public function update(Request $request, CmsEntry $entry): RedirectResponse
    {
        $locale = $this->locale($request, $entry);
        $entry->load('contents');

        $schema = ContentManager::schema($entry, $locale);
        $content = $schema->normalize(ContentManager::working($entry, $locale), FormPayload::content($request));
        $errors = $schema->validate($content);

        if ($errors) {
            return back()
                ->withErrors(collect($errors)->mapWithKeys(fn ($message, $path) => ['c.'.$path => $message])->all())
                ->with('cms_content_'.$entry->id.'_'.$locale, $content)
                ->with('error', 'Some fields need attention. Nothing was saved yet.');
        }

        ContentManager::saveDraft($entry, $locale, $content, $request->user());
        $language = Localization::LOCALES[$locale]['name'];

        return match ($request->input('action')) {
            'publish' => $this->publishNow($request, $entry, $locale),
            'preview' => redirect()->route('admin.entries.preview', [$entry, 'lang' => $locale]),
            default => redirect()->route('admin.entries.edit', [$entry, 'lang' => $locale])
                ->with('success', "Draft saved ({$language}). Visitors still see the published version until you publish."),
        };
    }

    public function publish(Request $request, CmsEntry $entry): RedirectResponse
    {
        return $this->publishNow($request, $entry, $this->locale($request, $entry));
    }

    private function publishNow(Request $request, CmsEntry $entry, string $locale): RedirectResponse
    {
        $published = ContentManager::publish($entry->fresh(), $locale, $request->user());
        $language = Localization::LOCALES[$locale]['name'];

        return redirect()->route('admin.entries.edit', [$entry, 'lang' => $locale])
            ->with($published ? 'success' : 'error', $published ? "Published ({$language}). The changes are now live." : 'There are no draft changes to publish.');
    }

    public function discard(Request $request, CmsEntry $entry): RedirectResponse
    {
        $locale = $this->locale($request, $entry);
        ContentManager::discard($entry, $locale);

        return redirect()->route('admin.entries.edit', [$entry, 'lang' => $locale])->with('success', 'Draft changes discarded.');
    }

    public function unpublish(Request $request, CmsEntry $entry): RedirectResponse
    {
        abort_unless(Registry::isCollection($entry), 403, 'Core pages cannot be taken offline.');
        $locale = $this->locale($request, $entry);
        ContentManager::unpublish($entry, $locale, $request->user());

        return redirect()->route('admin.entries.edit', [$entry, 'lang' => $locale])
            ->with('success', 'Taken offline in '.Localization::LOCALES[$locale]['name'].'. The content is kept as a draft.');
    }

    /** Shows the public page with the draft applied (only administrators can see this). */
    public function preview(Request $request, CmsEntry $entry): Response
    {
        $locale = $this->locale($request, $entry);
        $entry->load('contents');
        $content = ContentManager::working($entry, $locale);

        App::setLocale($locale);
        config(['site' => SiteContent::preview($locale, $entry, $content)]);

        $site = app(SiteController::class);
        $view = match ($entry->type) {
            'page' => $site->page($entry->slug),
            'service' => $site->service($entry->slug),
            'article' => $site->article($entry->slug),
            default => $site->home(),
        };

        $html = $view->render();
        if ($locale !== Localization::DEFAULT) {
            $html = Localization::localizeLinks($html, $locale);
        }

        $row = $entry->contentFor($locale);
        $state = $row->hasDraft() ? 'Draft preview: these changes are not live yet.' : 'Preview of the published version.';
        $banner = view('admin.entries.preview-banner', [
            'entry' => $entry,
            'locale' => $locale,
            'state' => $state,
            'label' => Registry::label($entry, $entry->contentFor(Localization::DEFAULT)->working()),
        ])->render();
        $html = preg_replace('/<body([^>]*)>/', '<body$1>'.$banner, $html, 1);
        $html = str_replace('<head>', "<head>\n    <meta name=\"robots\" content=\"noindex, nofollow\">", $html);

        return response($html)->header('Cache-Control', 'no-store, private')->header('Content-Language', $locale);
    }

    public function history(Request $request, CmsEntry $entry): View
    {
        $locale = $this->locale($request, $entry);

        return view('admin.entries.history', [
            'entry' => $entry,
            'locale' => $locale,
            'locales' => Registry::localesFor($entry->key),
            'label' => Registry::label($entry, $entry->contentFor(Localization::DEFAULT)->working()),
            'revisions' => $entry->revisions()->where('locale', $locale)->with('user')->paginate(10)->withQueryString(),
        ]);
    }

    public function restoreRevision(Request $request, CmsEntry $entry, CmsRevision $revision): RedirectResponse
    {
        abort_unless($revision->cms_entry_id === $entry->id, 404);
        ContentManager::restoreRevision($entry, $revision, $request->user());

        return redirect()->route('admin.entries.edit', [$entry, 'lang' => $revision->locale])
            ->with('success', 'The version from '.$revision->created_at->format('j M Y, H:i').' was restored as a draft. Review it, then publish.');
    }

    public function destroy(Request $request, CmsEntry $entry): RedirectResponse
    {
        abort_if($entry->is_required || ! Registry::isCollection($entry), 403, 'This content is required by the website and cannot be deleted.');

        $references = ContentManager::references($entry);
        if ($references) {
            return back()->with('error', 'This item is still linked from: '.implode(', ', $references).'. Remove those links first, then delete it.');
        }

        $entry->delete();
        SiteContent::flush();

        return redirect()->route('admin.collections.index', $entry->type)
            ->with('success', 'Moved to the bin. You can restore it from the “Deleted” filter.');
    }

    public function restore(CmsEntry $entry): RedirectResponse
    {
        if (CmsEntry::query()->where('key', $entry->key)->exists()) {
            return back()->with('error', 'Another item already uses this address.');
        }

        $entry->restore();
        SiteContent::flush();

        return redirect()->route('admin.entries.edit', $entry)->with('success', 'Restored. Its published languages are live again.');
    }

    private function locale(Request $request, CmsEntry $entry): string
    {
        $locale = (string) $request->input('lang', Localization::DEFAULT);

        return in_array($locale, Registry::localesFor($entry->key), true) ? $locale : Localization::DEFAULT;
    }
}
