<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinguistApplication;
use App\Models\NewsletterSubscriber;
use App\Models\ProjectEnquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Forms sent from the website: quote requests, linguist applications and newsletter sign-ups.
 */
class SubmissionController extends Controller
{
    public const TYPES = [
        'quotes' => [
            'model' => ProjectEnquiry::class,
            'label' => 'Quote requests',
            'singular' => 'quote request',
            'search' => ['name', 'email', 'organization', 'phone', 'message'],
            'filter' => 'service',
            'file' => ['attachment_path', 'attachment_name'],
            'readable' => true,
        ],
        'applications' => [
            'model' => LinguistApplication::class,
            'label' => 'Linguist applications',
            'singular' => 'application',
            'search' => ['name', 'email', 'location', 'language_pairs', 'phone'],
            'filter' => 'expertise',
            'file' => ['cv_path', 'cv_name'],
            'readable' => true,
        ],
        'subscribers' => [
            'model' => NewsletterSubscriber::class,
            'label' => 'Newsletter subscribers',
            'singular' => 'subscriber',
            'search' => ['email'],
            'filter' => null,
            'file' => null,
            'readable' => false,
        ],
    ];

    public function index(Request $request, string $type): View|StreamedResponse
    {
        $meta = self::TYPES[$type];
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'option' => (string) $request->query('option', ''),
            'status' => (string) $request->query('status', ''),
            'from' => $this->date($request->query('from')),
            'to' => $this->date($request->query('to')),
        ];

        $query = $this->filtered($type, $filters);

        if ($request->query('export') === 'csv') {
            return $this->export($type, $query);
        }

        return view('admin.submissions.index', [
            'type' => $type,
            'meta' => $meta,
            'types' => self::TYPES,
            'filters' => $filters,
            'items' => $query->paginate(20)->withQueryString(),
            'options' => $meta['filter']
                ? $meta['model']::query()->whereNotNull($meta['filter'])->distinct()->orderBy($meta['filter'])->pluck($meta['filter'])
                : collect(),
            'counts' => self::counts(),
        ]);
    }

    public function show(string $type, int $id): View
    {
        $meta = self::TYPES[$type];
        abort_unless($meta['readable'], 404);

        $item = $meta['model']::query()->findOrFail($id);
        if ($item->read_at === null) {
            $item->forceFill(['read_at' => now()])->save();
        }

        $siblings = $meta['model']::query()->orderByDesc('id');

        return view('admin.submissions.show', [
            'type' => $type,
            'meta' => $meta,
            'item' => $item,
            'newer' => (clone $siblings)->where('id', '>', $id)->reorder('id')->value('id'),
            'older' => (clone $siblings)->where('id', '<', $id)->value('id'),
        ]);
    }

    /** Uploaded files are private: only administrators can download them, through this route. */
    public function download(string $type, int $id)
    {
        $meta = self::TYPES[$type];
        abort_unless($meta['file'], 404);

        $item = $meta['model']::query()->findOrFail($id);
        [$pathField, $nameField] = $meta['file'];
        abort_unless($item->{$pathField} && Storage::disk('local')->exists($item->{$pathField}), 404, 'The file is no longer available.');

        return Storage::disk('local')->download($item->{$pathField}, $item->{$nameField} ?: basename($item->{$pathField}));
    }

    public function markUnread(string $type, int $id): RedirectResponse
    {
        $meta = self::TYPES[$type];
        abort_unless($meta['readable'], 404);
        $meta['model']::query()->whereKey($id)->update(['read_at' => null]);

        return redirect()->route('admin.submissions.index', $type)->with('success', 'Marked as unread.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $meta = self::TYPES[$type];
        $item = $meta['model']::query()->findOrFail($id);

        if ($meta['file'] && ($path = $item->{$meta['file'][0]})) {
            Storage::disk('local')->delete($path);
        }
        $item->delete();

        return redirect()->route('admin.submissions.index', $type)->with('success', ucfirst($meta['singular']).' deleted.');
    }

    /** Unread counts for the sidebar and tabs. */
    public static function counts(): array
    {
        return [
            'quotes' => ['total' => ProjectEnquiry::query()->count(), 'new' => ProjectEnquiry::query()->whereNull('read_at')->count()],
            'applications' => ['total' => LinguistApplication::query()->count(), 'new' => LinguistApplication::query()->whereNull('read_at')->count()],
            'subscribers' => ['total' => NewsletterSubscriber::query()->count(), 'new' => 0],
        ];
    }

    private function filtered(string $type, array $filters): Builder
    {
        $meta = self::TYPES[$type];

        return $meta['model']::query()
            ->when($filters['q'] !== '', fn (Builder $query) => $query->where(function (Builder $inner) use ($meta, $filters) {
                foreach ($meta['search'] as $column) {
                    $inner->orWhere($column, 'like', '%'.$filters['q'].'%');
                }
            }))
            ->when($meta['filter'] && $filters['option'] !== '', fn (Builder $query) => $query->where($meta['filter'], $filters['option']))
            ->when($meta['readable'] && $filters['status'] === 'new', fn (Builder $query) => $query->whereNull('read_at'))
            ->when($meta['readable'] && $filters['status'] === 'read', fn (Builder $query) => $query->whereNotNull('read_at'))
            ->when($filters['from'], fn (Builder $query) => $query->where('created_at', '>=', Carbon::parse($filters['from'])->startOfDay()))
            ->when($filters['to'], fn (Builder $query) => $query->where('created_at', '<=', Carbon::parse($filters['to'])->endOfDay()))
            ->latest('id');
    }

    private function export(string $type, Builder $query): StreamedResponse
    {
        $columns = match ($type) {
            'quotes' => ['Received' => 'created_at', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Organization' => 'organization', 'Service' => 'service', 'Industry' => 'industry', 'Languages' => 'languages', 'Other language' => 'other_language', 'Volume' => 'volume', 'Deadline' => 'deadline', 'Message' => 'message', 'File' => 'attachment_name'],
            'applications' => ['Received' => 'created_at', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Location' => 'location', 'Native languages' => 'native_languages', 'Language pairs' => 'language_pairs', 'Expertise' => 'expertise', 'Tools' => 'tools', 'CV' => 'cv_name'],
            default => ['Subscribed' => 'created_at', 'Email' => 'email'],
        };

        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly
            fputcsv($out, array_keys($columns));
            $query->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    fputcsv($out, array_map(fn ($column) => $this->cell($row, $column), $columns));
                }
            });
            fclose($out);
        }, "nile-lingua-{$type}-".now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function cell(Model $row, string $column): string
    {
        $value = $row->{$column};
        $text = match (true) {
            is_array($value) => implode(', ', $value),
            $value instanceof \DateTimeInterface => $value->format('Y-m-d H:i'),
            default => (string) $value,
        };

        // Stop spreadsheet apps from treating a value as a formula.
        if (preg_match('/^\+[\d\s().-]+$/', $text)) {
            return $text; // a phone number
        }

        return preg_match('/^[=+\-@\t\r]/', $text) ? "'".$text : $text;
    }

    private function date(mixed $value): ?string
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
