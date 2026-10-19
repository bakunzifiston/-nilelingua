<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use App\Models\Media;
use App\Support\SvgSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Images used in managed content. Files are stored in public/images/uploads.
 */
class MediaController extends Controller
{
    /** Largest image the media library accepts (KB). */
    public const MAX_KB = 10240;

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
        $limit = self::serverUploadLimitMb();
        $data = $request->validate([
            'file' => ['required', 'file', 'max:'.self::MAX_KB, 'extensions:jpg,jpeg,png,webp,gif,svg'],
            'alt_en' => ['nullable', 'string', 'max:255'],
            'alt_fr' => ['nullable', 'string', 'max:255'],
        ], [
            'file.extensions' => 'Upload a JPG, PNG, WebP, GIF or SVG image.',
            'file.max' => 'Images must be '.(self::MAX_KB / 1024).' MB or smaller.',
            'file.uploaded' => "The file did not reach the server. It may be larger than the server's upload limit ({$limit} MB), or the connection was interrupted.",
        ]);

        $file = $request->file('file');
        $isSvg = strtolower($file->getClientOriginalExtension()) === 'svg';

        // The content must match the extension: a real bitmap, or an SVG we can clean.
        if ($isSvg) {
            $svg = SvgSanitizer::clean((string) file_get_contents($file->getRealPath()));
            if ($svg === null) {
                return back()->withErrors(['file' => 'This SVG file could not be read. Export it again as a plain SVG and try once more.'])->withInput();
            }
        } elseif (! in_array(@getimagesize($file->getRealPath())['mime'] ?? null, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
            // Checked from the file's actual content, not its name or the type the browser reports.
            return back()->withErrors(['file' => 'Upload a JPG, PNG, WebP, GIF or SVG image.'])->withInput();
        }

        // Inside public/images: hosts that only forward known asset folders already serve it.
        $folder = 'images/uploads/'.now()->format('Y/m');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $name = Str::limit($name, 60, '').'-'.Str::lower(Str::random(6)).'.'.($isSvg ? 'svg' : $file->guessExtension());

        File::ensureDirectoryExists(public_path($folder));
        self::protectUploadsFolder();

        if ($isSvg) {
            File::put(public_path($folder.'/'.$name), $svg);
        } else {
            $file->move(public_path($folder), $name);
        }

        $path = $folder.'/'.$name;
        [$width, $height] = $isSvg ? SvgSanitizer::dimensions($svg) : (@getimagesize(public_path($path)) ?: [null, null]);

        Media::create([
            'path' => $path,
            'original_name' => Str::limit($file->getClientOriginalName(), 250, ''),
            'mime_type' => $isSvg ? 'image/svg+xml' : (File::mimeType(public_path($path)) ?: 'application/octet-stream'),
            'size' => File::size(public_path($path)),
            'width' => $width,
            'height' => $height,
            'alt_en' => $data['alt_en'] ?? null,
            'alt_fr' => $data['alt_fr'] ?? null,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Image uploaded. Choose it in any photo field.');
    }

    /** Uploaded files are only ever served as plain files: no scripts, no PHP. */
    private static function protectUploadsFolder(): void
    {
        // Only directives every Apache/LiteSpeed host allows here (no "Options", which some hosts forbid).
        $rules = implode("\n", [
            '# Uploaded media: never run scripts from this folder.',
            '<FilesMatch "\\.(php|phtml|phar|pl|py|cgi|sh)$">',
            '    Require all denied',
            '</FilesMatch>',
            '<IfModule mod_headers.c>',
            '    <FilesMatch "\\.svg$">',
            '        Header set Content-Security-Policy "default-src \'none\'; style-src \'unsafe-inline\'; img-src data:"',
            '    </FilesMatch>',
            '    Header set X-Content-Type-Options "nosniff"',
            '</IfModule>',
            '',
        ]);

        $file = public_path('images/uploads/.htaccess');
        if (! is_file($file) || File::get($file) !== $rules) {
            File::put($file, $rules);
        }
    }

    /** The smaller of PHP's two upload limits, in MB. */
    public static function serverUploadLimitMb(): float
    {
        $toMb = function (string $value): float {
            $number = (float) $value;

            return match (strtolower(substr(trim($value), -1))) {
                'g' => $number * 1024,
                'k' => $number / 1024,
                'm' => $number,
                default => $number / 1048576,
            };
        };

        return round(min($toMb((string) ini_get('upload_max_filesize')), $toMb((string) ini_get('post_max_size'))), 1);
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
