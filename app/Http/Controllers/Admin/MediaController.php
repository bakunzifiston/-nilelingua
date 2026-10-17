<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Images used in managed content. Files are stored in public/uploads.
 */
class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        return view('admin.media.index', [
            'media' => Media::query()
                ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner
                    ->where('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_en', 'like', "%{$search}%")
                    ->orWhere('alt_fr', 'like', "%{$search}%")))
                ->latest()->paginate(24)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'alt_en' => ['nullable', 'string', 'max:255'],
            'alt_fr' => ['nullable', 'string', 'max:255'],
        ], [
            'file.mimes' => 'Upload a JPG, PNG, WebP or GIF image.',
            'file.max' => 'Images must be 5 MB or smaller.',
        ]);

        $file = $request->file('file');
        $folder = 'uploads/'.now()->format('Y/m');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $name = Str::limit($name, 60, '').'-'.Str::lower(Str::random(6)).'.'.$file->guessExtension();

        File::ensureDirectoryExists(public_path($folder));
        $file->move(public_path($folder), $name);

        $path = $folder.'/'.$name;
        [$width, $height] = @getimagesize(public_path($path)) ?: [null, null];

        Media::create([
            'path' => $path,
            'original_name' => Str::limit($file->getClientOriginalName(), 250, ''),
            'mime_type' => File::mimeType(public_path($path)) ?: 'application/octet-stream',
            'size' => File::size(public_path($path)),
            'width' => $width,
            'height' => $height,
            'alt_en' => $data['alt_en'] ?? null,
            'alt_fr' => $data['alt_fr'] ?? null,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Image uploaded. Choose it in any photo field.');
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $media->update($request->validate([
            'alt_en' => ['nullable', 'string', 'max:255'],
            'alt_fr' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Image description saved.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        $needle = json_encode($media->path, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $inUse = CmsContent::query()->whereHas('entry')->get()->contains(fn (CmsContent $content) => collect([$content->published, $content->draft])
            ->filter()->contains(fn ($version) => str_contains(json_encode($version, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $needle)));

        if ($inUse) {
            return back()->with('error', 'This image is used in published or draft content. Replace it there first, then delete it.');
        }

        File::delete(public_path($media->path));
        $media->delete();

        return back()->with('success', 'Image deleted.');
    }
}
