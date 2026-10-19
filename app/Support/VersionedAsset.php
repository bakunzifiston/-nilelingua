<?php

namespace App\Support;

/**
 * Asset URLs with a version stamp (?v=…) that changes whenever the file changes,
 * so browsers fetch the new file after an update instead of using a cached copy.
 */
class VersionedAsset
{
    public static function url(string $path): string
    {
        $file = public_path($path);
        $version = is_file($file) ? substr(md5((string) filemtime($file).filesize($file)), 0, 10) : null;

        return asset($path).($version ? '?v='.$version : '');
    }
}
