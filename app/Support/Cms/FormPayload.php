<?php

namespace App\Support\Cms;

use Illuminate\Http\Request;

/**
 * Reads the submitted content form.
 *
 * Long pages have more fields than PHP's max_input_vars allows, so the editor's script sends the
 * fields as one JSON list of [name, value] pairs ("c_json"). Without JavaScript the regular
 * "c[...]" fields are used.
 */
class FormPayload
{
    public static function content(Request $request): array
    {
        $json = $request->input('c_json');
        if (! is_string($json) || $json === '') {
            return (array) $request->input('c', []);
        }

        $pairs = json_decode($json, true);
        if (! is_array($pairs)) {
            return [];
        }

        $data = [];
        foreach ($pairs as $pair) {
            if (! is_array($pair) || count($pair) !== 2 || ! is_string($pair[0])) {
                continue;
            }
            [$name, $value] = $pair;
            if (! preg_match('/^c((?:\[[^\[\]]*\])+)$/', $name, $match)) {
                continue;
            }
            preg_match_all('/\[([^\[\]]*)\]/', $match[1], $segments);

            $node = &$data;
            foreach ($segments[1] as $segment) {
                if (! is_array($node)) {
                    $node = [];
                }
                $node[$segment] ??= null;
                $node = &$node[$segment];
            }
            $node = is_scalar($value) ? (string) $value : null;
            unset($node);
        }

        return $data;
    }
}
