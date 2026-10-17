<?php

namespace App\Support\Cms;

/**
 * Small helpers for walking nested content arrays by dot paths ("*" matches any key).
 */
class Tree
{
    /** Concrete paths in $data matching a pattern such as "social.*.url". */
    public static function expand(array $data, string $pattern): array
    {
        $paths = [''];
        foreach (explode('.', $pattern) as $segment) {
            $next = [];
            foreach ($paths as $path) {
                $node = $path === '' ? $data : data_get($data, $path);
                if (! is_array($node)) {
                    continue;
                }
                $keys = $segment === '*' ? array_keys($node) : (array_key_exists($segment, $node) ? [$segment] : []);
                foreach ($keys as $key) {
                    $next[] = ltrim($path.'.'.$key, '.');
                }
            }
            $paths = $next;
        }

        return $paths;
    }

    /** Copies language-independent values from the source content into the target content. */
    public static function copyShared(array $target, array $source, array $patterns): array
    {
        foreach ($patterns as $pattern) {
            foreach (self::expand($source, $pattern) as $path) {
                $parent = str_contains($path, '.') ? substr($path, 0, strrpos($path, '.')) : null;
                if ($parent === null || is_array(data_get($target, $parent))) {
                    data_set($target, $path, data_get($source, $path));
                }
            }
        }

        return $target;
    }

    /** Does a path (e.g. "social.linkedin.url") match one of the patterns? */
    public static function matches(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $regex = '/^'.str_replace('\*', '[^.]+', preg_quote($pattern, '/')).'$/';
            if (preg_match($regex, $path)) {
                return true;
            }
        }

        return false;
    }

    /** All text values, keyed by dot path. */
    public static function strings(array $data, string $prefix = ''): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $out += self::strings($value, $path);
            } elseif (is_string($value)) {
                $out[$path] = $value;
            }
        }

        return $out;
    }

    /** Pattern form of a concrete path: list indexes become "*". */
    public static function pattern(string $path): string
    {
        return preg_replace('/(^|\.)\d+(?=\.|$)/', '$1*', $path);
    }
}
