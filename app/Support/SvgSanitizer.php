<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Cleans uploaded SVG images so they can be shown safely: only drawing elements and
 * presentation attributes are kept; scripts, event handlers, embedded HTML and links to
 * other files are removed.
 */
class SvgSanitizer
{
    private const ELEMENTS = [
        'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'metadata', 'style',
        'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'textpath',
        'lineargradient', 'radialgradient', 'stop', 'pattern', 'clippath', 'mask', 'marker',
        'filter', 'feblend', 'fecolormatrix', 'fecomponenttransfer', 'fecomposite', 'feflood',
        'fegaussianblur', 'feimage', 'femerge', 'femergenode', 'feoffset', 'fedropshadow',
        'fefunca', 'fefuncb', 'fefuncg', 'fefuncr', 'femorphology', 'feturbulence',
        'image',
    ];

    /** Removed together with everything inside them. */
    private const DANGEROUS = [
        'script', 'foreignobject', 'iframe', 'object', 'embed', 'audio', 'video', 'canvas',
        'html', 'body', 'handler', 'listener', 'animate', 'animatemotion', 'animatetransform',
        'animatecolor', 'set', 'discard',
    ];

    /**
     * Design tools (notably Illustrator) add a DOCTYPE with a few plain text entities such as
     * &ns_ai;. Those are replaced by their text and the DOCTYPE dropped; anything else
     * (external or nested entities) is refused.
     */
    private static function resolveDoctype(string $svg): ?string
    {
        if (! preg_match('/<!DOCTYPE\b/i', $svg)) {
            return preg_match('/<!ENTITY\b/i', $svg) ? null : $svg;
        }
        if (! preg_match('/<!DOCTYPE[^\[>]*(\[(.*?)\])?\s*>/is', $svg, $doctype)) {
            return null;
        }

        $entities = [];
        $subset = $doctype[2] ?? '';
        preg_match_all('/<!ENTITY\s+([A-Za-z_][\w.-]*)\s+"([^"&%<]*)"\s*>/', $subset, $found, PREG_SET_ORDER);
        foreach ($found as [$whole, $name, $value]) {
            $entities['&'.$name.';'] = htmlspecialchars($value, ENT_QUOTES | ENT_XML1);
            $subset = str_replace($whole, '', $subset);
        }
        // Anything left in the internal subset (external entities, parameter entities…) is not allowed.
        if (trim(preg_replace('/<!--.*?-->/s', '', $subset)) !== '') {
            return null;
        }

        $svg = str_replace($doctype[0], '', $svg);

        return strtr($svg, $entities);
    }

    /**
     * Returns the cleaned SVG markup, or null when the file is not a usable SVG image.
     */
    public static function clean(string $svg): ?string
    {
        $svg = self::resolveDoctype($svg);
        if ($svg === null || trim($svg) === '') {
            return null;
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($svg, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_PARSEHUGE);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->documentElement;
        if (! $loaded || ! $root || strtolower($root->localName) !== 'svg') {
            return null;
        }

        self::cleanNode($root);

        return $document->saveXML($root);
    }

    /** Width and height from the attributes or the viewBox. */
    public static function dimensions(string $svg): array
    {
        $document = new DOMDocument;
        $svg = self::resolveDoctype($svg) ?? '';
        if ($svg === '' || ! @$document->loadXML($svg, LIBXML_NONET | LIBXML_PARSEHUGE) || ! $document->documentElement) {
            return [null, null];
        }
        $root = $document->documentElement;
        $width = (int) round((float) $root->getAttribute('width'));
        $height = (int) round((float) $root->getAttribute('height'));
        if ((! $width || ! $height) && preg_match('/^\s*[-\d.]+[\s,]+[-\d.]+[\s,]+([\d.]+)[\s,]+([\d.]+)/', $root->getAttribute('viewBox'), $box)) {
            [$width, $height] = [(int) round((float) $box[1]), (int) round((float) $box[2])];
        }

        return [$width ?: null, $height ?: null];
    }

    private static function cleanNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->localName);
                if (in_array($tag, self::DANGEROUS, true)) {
                    $node->removeChild($child);

                    continue;
                }
                if (! in_array($tag, self::ELEMENTS, true)) {
                    // Unknown wrappers (Illustrator's <switch>, links, editor groups): keep the artwork inside.
                    self::cleanNode($child);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);

                    continue;
                }
                self::cleanAttributes($child);
                if (strtolower($child->localName) === 'style') {
                    $child->textContent = self::cleanCss($child->textContent);

                    continue;
                }
                self::cleanNode($child);
            } elseif ($child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
            }
        }

        if ($node instanceof DOMElement && strtolower($node->localName) === 'svg') {
            self::cleanAttributes($node);
        }
    }

    private static function cleanAttributes(DOMElement $element): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->localName);
            $value = trim($attribute->value);

            $remove = str_starts_with($name, 'on')
                || ($name === 'href' && ! self::isSafeReference($value))
                || ($name === 'style' && $value !== self::cleanCss($value))
                || preg_match('/(javascript|vbscript|data:text)/i', $value);

            if ($remove) {
                $element->removeAttributeNode($attribute);
            }
        }
    }

    /** Links may only point inside the image (#id) or to an embedded bitmap. */
    private static function isSafeReference(string $value): bool
    {
        return str_starts_with($value, '#') || preg_match('#^data:image/(png|jpe?g|gif|webp);base64,#i', $value);
    }

    /** CSS may not load other files or run expressions. */
    private static function cleanCss(string $css): string
    {
        $css = preg_replace('/@import[^;]*;?/i', '', $css);
        $css = preg_replace('/url\(\s*([\'"]?)(?!#)[^)]*\)/i', 'none', $css);

        return preg_replace('/(expression|javascript|behavior)\s*[:(]/i', '', $css);
    }
}
