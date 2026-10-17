<?php

namespace App\Support\Cms;

use App\Models\CmsEntry;
use App\Models\Media;
use App\Support\Localization;
use Illuminate\Support\Facades\File;

/**
 * Turns an entry's content structure into an editing form and back.
 *
 * Pages differ in shape, so the form is derived from the content itself: text becomes inputs,
 * lists become repeatable, reorderable items, and nested groups become fieldsets. Submitted data is
 * merged onto the known structure, so unknown keys are ignored and hidden values are preserved.
 */
class EditorSchema
{
    /** SEO fields shown together at the top of the form. */
    public const SEO_KEYS = ['title', 'seo_title', 'meta', 'keyphrase'];

    private const LABELS = [
        'title' => 'Title',
        'seo_title' => 'SEO title (browser tab and search results)',
        'meta' => 'Meta description',
        'keyphrase' => 'Focus keyphrase',
        'eyebrow' => 'Eyebrow (small label above the heading)',
        'headline' => 'Main heading (H1)',
        'subheading' => 'Subheading',
        'subhead' => 'Subheading',
        'intro' => 'Introduction',
        'href' => 'Link URL',
        'url' => 'URL',
        'cta' => 'Call-to-action text',
        'cta_heading' => 'Call-to-action heading',
        'cta_note' => 'Call-to-action note',
        'aside' => 'Supporting text (beside the heading)',
        'stat_number' => 'Statistic (number)',
        'stat_label' => 'Statistic label',
        'stat_caption' => 'Statistic caption',
        'visible' => 'Show this section on the website',
        'style' => 'Card style',
        'kicker' => 'Small label',
        'background' => 'Background',
        'primary_button' => 'Main button',
        'secondary_button' => 'Second button',
        'scroll_label' => 'Scroll link text',
        'highlight' => 'Highlighted phrase (must appear in the heading)',
        'photo' => 'Photo',
        'linkedin' => 'LinkedIn profile URL',
        'max_upload_kb' => 'Maximum upload size (KB)',
        'file_types' => 'Accepted file types (extensions)',
        'read_time' => 'Reading time',
        'published' => 'Published date',
        'updated' => 'Last updated date',
        'wide' => 'Wide card',
        'items' => 'Items',
        'name' => 'Name',
        'maps_url' => 'Map link (Google Maps URL)',
        'whatsapp_url' => 'WhatsApp link',
        'address_line_1' => 'Address line 1',
        'address_line_2' => 'Address line 2',
        'hours_days' => 'Opening days',
        'hours_time' => 'Opening hours',
        'link_word' => 'Linked word (inside the text)',
        'link_href' => 'Linked word URL',
        'questions_email' => 'Questions email address',
        'effective_date' => 'Effective date',
        'groups' => 'Groups',
        'blocks' => 'Content blocks',
        'head' => 'Table header cells',
        'rows' => 'Table rows',
        'text' => 'Text',
        'body' => 'Text',
    ];

    private const HELP = [
        'meta' => 'Shown under the page title in search results. Aim for 120–160 characters.',
        'seo_title' => 'Aim for 50–60 characters, with the main keyphrase near the start.',
        'title' => 'Used in the browser tab, menus and breadcrumbs.',
        'keyphrase' => 'The main search phrase this page targets.',
        'href' => 'Use a site path such as /contact, or a full address starting with https://.',
        'highlight' => 'This exact phrase in the heading is kept on one line.',
        'link_word' => 'This exact word or phrase in the text becomes a link.',
        'visible' => 'Untick to hide the section without deleting its content.',
        'file_types' => 'Lowercase extensions without the dot, e.g. pdf.',
    ];

    /** Long-text keys (always a multi-line field). */
    private const LONG_KEYS = ['meta', 'intro', 'text', 'body', 'summary', 'challenge', 'cta', 'answer', 'outro', 'mission', 'vision', 'base', 'how_we_work', 'help', 'note', 'paragraph', 'hero_copy', 'supporting_copy', 'partner_copy', 'languages_copy'];

    /** Fixed choices for some keys. */
    private const CHOICES = [
        'style' => ['localization' => 'Localization (globe)', 'translation' => 'Translation (document)', 'audiovisual' => 'Audiovisual (play)', 'data' => 'AI data (waveform)'],
        'background' => ['white' => 'White', 'sand' => 'Light grey'],
    ];

    private const SECTION_TYPES = [
        'hero' => 'Hero banner',
        'services' => 'Services',
        'languages' => 'Language coverage',
        'why' => 'Why choose us',
        'tools' => 'Tools & file compatibility',
        'cta' => 'Call to action',
        'text' => 'Text section',
        'p' => 'Paragraph',
        'h3' => 'Subheading',
        'ul' => 'Bulleted list',
        'ol' => 'Numbered list',
        'table' => 'Table',
        'note' => 'Note',
    ];

    /** Pattern => [signature => ['template' => array|string, 'label' => string]] */
    private array $variants = [];

    private static ?array $icons = null;

    private ?array $mediaOptions = null;

    public function __construct(
        public readonly CmsEntry $entry,
        public readonly string $locale,
        array $sources,
    ) {
        foreach ($sources as $source) {
            if (is_array($source)) {
                $this->collect($source, '');
            }
        }

        // A plain text section can always be added to the homepage.
        if ($entry->type === 'home') {
            $this->collect([[
                'type' => 'text', 'visible' => true, 'background' => 'white', 'eyebrow' => '', 'heading' => '',
                'paragraphs' => [''], 'primary_button' => ['label' => '', 'href' => ''], 'secondary_button' => ['label' => '', 'href' => ''],
            ]], 'sections');
        }
    }

    // ---------------------------------------------------------------------------------------------
    // Structure

    private function collect(array $node, string $pattern): void
    {
        if (array_is_list($node)) {
            foreach ($node as $item) {
                $this->register($this->join($pattern, '*'), self::blank($item));
                if (is_array($item)) {
                    $this->collect($item, $this->itemPattern($pattern, $item));
                }
            }

            return;
        }

        foreach ($node as $key => $value) {
            if (is_array($value)) {
                $this->collect($value, $this->join($pattern, (string) $key));
            }
        }
    }

    private function register(string $pattern, mixed $template): void
    {
        $signature = self::signature($template);
        $this->variants[$pattern][$signature] ??= ['template' => $template, 'label' => ''];
    }

    /**
     * Pattern for the fields inside a list item. Items with a type (homepage sections, article
     * blocks) get their own pattern, so each type offers only its own kind of sub-items.
     */
    public function itemPattern(string $listPattern, mixed $item): string
    {
        $type = is_array($item) && isset($item['type']) && is_string($item['type']) ? '['.$item['type'].']' : '';

        return $this->join($listPattern, '*').$type;
    }

    private function join(string $pattern, string $segment): string
    {
        return $pattern === '' ? $segment : $pattern.'.'.$segment;
    }

    public static function signature(mixed $value): string
    {
        if (! is_array($value)) {
            return 's';
        }
        if (array_is_list($value)) {
            return 'l';
        }
        $keys = array_keys($value);
        sort($keys);

        return substr(md5(implode('|', $keys).'#'.($value['type'] ?? '')), 0, 10);
    }

    /** An empty copy of a value (keeps the block/section type and sensible defaults). */
    public static function blank(mixed $value): mixed
    {
        if (is_array($value)) {
            if (array_is_list($value)) {
                return [];
            }
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = match (true) {
                    $key === 'type' => $item,
                    $key === 'visible' => true,
                    $key === 'style', $key === 'background' => array_key_first(self::CHOICES[$key]),
                    default => self::blank($item),
                };
            }

            return $out;
        }

        return match (true) {
            is_bool($value) => false,
            is_int($value) => 0,
            default => '',
        };
    }

    /** Ways to add an item to a list: [signature => label]. */
    public function variantsFor(string $listPattern): array
    {
        $variants = $this->variants[$this->join($listPattern, '*')] ?? [];
        if ($variants === []) {
            return [];
        }

        // Describe each variant by its type, or by what it adds over the simplest variant.
        $templates = array_map(fn ($variant) => $variant['template'], $variants);
        $assoc = array_filter($templates, fn ($template) => is_array($template) && ! array_is_list($template));
        $base = $assoc ? array_reduce($assoc, fn ($carry, $template) => $carry === null || count($template) < count($carry) ? $template : $carry) : null;

        $labels = [];
        foreach ($variants as $signature => $variant) {
            $template = $variant['template'];
            if (is_array($template) && isset($template['type'])) {
                $labels[$signature] = self::SECTION_TYPES[$template['type']] ?? str($template['type'])->headline()->toString();
            } elseif (is_array($template) && ! array_is_list($template) && $base !== null && count($variants) > 1) {
                $extra = array_diff(array_keys($template), array_keys($base));
                $extra = array_slice(array_values($extra), 0, 2);
                $labels[$signature] = $extra ? 'Item with '.implode(' and ', array_map(fn ($key) => mb_strtolower($this->label($key)), $extra)) : 'Item';
            } else {
                $labels[$signature] = 'Item';
            }
        }

        // Required homepage sections cannot be added twice.
        if ($this->entry->type === 'home' && $listPattern === 'sections') {
            foreach ($variants as $signature => $variant) {
                if (in_array($variant['template']['type'] ?? null, Registry::REQUIRED_HOME_SECTIONS, true)) {
                    unset($labels[$signature]);
                }
            }
        }

        return $labels;
    }

    public function template(string $listPattern, string $signature): mixed
    {
        return $this->variants[$this->join($listPattern, '*')][$signature]['template'] ?? null;
    }

    // ---------------------------------------------------------------------------------------------
    // Presentation

    public function label(string $key): string
    {
        if (preg_match('/\p{Lu}/u', $key)) {
            return $key; // a name used as a key, e.g. a language
        }

        return self::LABELS[$key] ?? str($key)->replace(['_', '-'], ' ')->ucfirst()->toString();
    }

    public function help(string $key, string $pattern): ?string
    {
        if ($this->entry->type === 'home' && in_array($key, ['heading'], true)) {
            return 'Wrap words in *asterisks* to show them in gold italics. Press Enter for a line break.';
        }
        if ($this->entry->type === 'article' && preg_match('/^(sections\.\*\.blocks|closing\.body)/', $pattern)) {
            return 'Formatting: **bold**, *italic*, [link text](/page-path).';
        }

        return self::HELP[$key] ?? null;
    }

    public function sectionTypeLabel(?string $type): ?string
    {
        return $type === null ? null : (self::SECTION_TYPES[$type] ?? str($type)->headline()->toString());
    }

    /** How a value is edited: text, textarea, checkbox, number, icon, choice, media. */
    public function kind(string $key, mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'checkbox',
            is_int($value) => 'number',
            $key === 'icon' => 'icon',
            isset(self::CHOICES[$key]) => 'choice',
            in_array($key, ['photo', 'image'], true) => 'media',
            in_array($key, self::LONG_KEYS, true), mb_strlen((string) $value) > 90, str_contains((string) $value, "\n") => 'textarea',
            default => 'text',
        };
    }

    public function choices(string $key): array
    {
        return self::CHOICES[$key] ?? [];
    }

    public static function icons(): array
    {
        if (self::$icons === null) {
            preg_match_all("/@case\\('([a-z-]+)'\\)/", File::get(resource_path('views/components/icon.blade.php')), $matches);
            self::$icons = array_values(array_diff($matches[1], ['linkedin', 'facebook', 'instagram', 'x', 'whatsapp']));
        }

        return self::$icons;
    }

    public function mediaOptions(): array
    {
        return $this->mediaOptions ??= Media::query()->where('mime_type', 'like', 'image/%')->latest()->get()
            ->mapWithKeys(fn (Media $media) => [$media->path => $media->original_name])->all();
    }

    public function isHidden(string $key): bool
    {
        return in_array($key, Registry::HIDDEN_KEYS, true);
    }

    /** Shared (language-independent) values are edited in English only. */
    public function isLocked(string $path): bool
    {
        return $this->locale !== Localization::DEFAULT && Tree::matches($path, Registry::sharedPaths($this->entry->key));
    }

    public function isShared(string $path): bool
    {
        return Tree::matches($path, Registry::sharedPaths($this->entry->key));
    }

    public function canRemove(string $listPattern, mixed $item): bool
    {
        if ($this->entry->type === 'home' && $listPattern === 'sections') {
            return ! in_array($item['type'] ?? null, Registry::REQUIRED_HOME_SECTIONS, true);
        }

        return true;
    }

    /** Short title for a collapsed list item. */
    public static function summary(mixed $item): string
    {
        if (! is_array($item)) {
            return (string) $item;
        }
        foreach (['title', 'heading', 'headline', 'label', 'question', 'name', 'region', 'area', 'topic', 'text', 'body', 'generic', 'without'] as $key) {
            if (isset($item[$key]) && is_string($item[$key]) && trim($item[$key]) !== '') {
                return str(Markup::plain($item[$key]))->limit(90)->toString();
            }
        }
        foreach ($item as $key => $value) {
            if ($key === 'type' || $key === 'style' || $key === 'icon') {
                continue;
            }
            if (is_string($value) && trim($value) !== '') {
                return str(Markup::plain($value))->limit(90)->toString();
            }
            if (is_array($value) && array_is_list($value) && isset($value[0]) && is_string($value[0]) && $value[0] !== '') {
                return str($value[0])->limit(90)->toString();
            }
        }

        return '';
    }

    /**
     * Top-level panels of the form: [['title', 'keys' => [...], 'description']].
     */
    public function panels(array $content): array
    {
        $panels = [];
        $seo = array_values(array_filter(self::SEO_KEYS, fn ($key) => array_key_exists($key, $content)));
        if ($seo) {
            $panels[] = ['id' => 'seo', 'title' => 'Search engine (SEO)', 'description' => 'How this page appears in search results and browser tabs.', 'keys' => $seo];
        }

        $fields = [];
        foreach ($content as $key => $value) {
            if (in_array($key, $seo, true) || $this->isHidden((string) $key)) {
                continue;
            }
            if (is_array($value)) {
                if ($fields) {
                    $panels[] = ['id' => 'text-'.count($panels), 'title' => count($panels) <= 1 ? 'Page header & main text' : 'Text', 'description' => null, 'keys' => $fields];
                    $fields = [];
                }
                $panels[] = ['id' => 'group-'.$key, 'title' => $this->label((string) $key), 'description' => null, 'keys' => [$key]];
            } else {
                $fields[] = $key;
            }
        }
        if ($fields) {
            $panels[] = ['id' => 'text-'.count($panels), 'title' => 'More text', 'description' => null, 'keys' => $fields];
        }

        return $panels;
    }

    // ---------------------------------------------------------------------------------------------
    // Saving

    /**
     * Merges submitted values onto the existing structure.
     */
    public function normalize(mixed $current, mixed $input, string $pattern = '', string $path = ''): mixed
    {
        if (is_array($current) && array_is_list($current)) {
            if (! is_array($input)) {
                return $current;
            }
            $out = [];
            foreach (array_values($input) as $index => $item) {
                $itemPattern = $this->join($pattern, '*');
                $template = null;
                if (is_array($item) && isset($item['__v'])) {
                    $template = $this->variants[$itemPattern][$item['__v']]['template'] ?? null;
                    unset($item['__v']);
                    if ($template === null) {
                        continue; // unknown item shape: ignore it
                    }
                }
                // Items without a known shape are plain text, or a list of text (e.g. table rows).
                $template ??= is_array($item) ? ($this->firstListTemplate($itemPattern) ?? []) : '';
                $out[] = $this->normalize($template, $item, $this->itemPattern($pattern, $template), $this->join($path, (string) $index));
            }

            return $out;
        }

        if (is_array($current)) {
            if (! is_array($input)) {
                return $current;
            }

            // A dictionary (e.g. language name => description) keeps the submitted entries.
            if (self::isDictionary($current)) {
                $out = [];
                foreach ($input as $name => $text) {
                    if (is_scalar($text) && preg_match('/^[\p{L}\p{N} ()&\'’.,\/-]{1,120}$/u', (string) $name)) {
                        $out[(string) $name] = trim(str_replace(["\r\n", "\r"], "\n", (string) $text));
                    }
                }

                return $out ?: $current;
            }

            $out = $current;
            foreach ($current as $key => $value) {
                $childPath = $this->join($path, (string) $key);
                if ($this->isHidden((string) $key) || $key === 'type' || $this->isLocked($childPath)) {
                    continue;
                }
                if (array_key_exists($key, $input)) {
                    $out[$key] = $this->normalize($value, $input[$key], $this->join($pattern, (string) $key), $childPath);
                }
            }

            return $out;
        }

        if (is_bool($current)) {
            return filter_var($input, FILTER_VALIDATE_BOOLEAN);
        }
        if (is_int($current)) {
            return is_numeric($input) ? (int) $input : $current;
        }
        if ($input === null) {
            return ''; // an emptied field (Laravel turns empty inputs into null)
        }
        if (! is_scalar($input)) {
            return $current;
        }

        return trim(str_replace(["\r\n", "\r"], "\n", (string) $input));
    }

    private static ?array $knownPaths = null;

    /** Does an internal link (e.g. "/services/subtitling#steps" or "/fr/contact") lead to a page? */
    public static function pathExists(string $link): bool
    {
        $path = '/'.trim((string) parse_url($link, PHP_URL_PATH), '/');
        $path = Localization::stripPrefix($path);

        if (self::$knownPaths === null) {
            self::$knownPaths = ['/', '/thank-you'];
            foreach (CmsEntry::query()->get(['type', 'slug']) as $entry) {
                if ($known = Registry::publicPath($entry)) {
                    self::$knownPaths[] = $known;
                }
            }
        }

        // Files in the public folder (images, uploads) are valid link targets too.
        return in_array($path, self::$knownPaths, true) || ($path !== '/' && is_file(public_path(ltrim($path, '/'))));
    }

    /** A map keyed by names (not field names), such as language => description. */
    public static function isDictionary(array $value): bool
    {
        if ($value === [] || array_is_list($value)) {
            return false;
        }
        foreach ($value as $key => $item) {
            if (! is_string($item)) {
                return false;
            }
        }

        return (bool) array_filter(array_keys($value), fn ($key) => ! preg_match('/^[a-z][a-z0-9_-]*$/', (string) $key));
    }

    private function firstListTemplate(string $itemPattern): mixed
    {
        foreach ($this->variants[$itemPattern] ?? [] as $variant) {
            if (is_array($variant['template']) && array_is_list($variant['template'])) {
                return $variant['template'];
            }
        }

        return null;
    }

    /**
     * Validates normalized content. Returns [dot path => message].
     */
    public function validate(array $content): array
    {
        $errors = [];

        foreach (['title', 'headline'] as $key) {
            if (array_key_exists($key, $content) && trim((string) $content[$key]) === '' && $this->locale === Localization::DEFAULT) {
                $errors[$key] = 'This field is required.';
            }
        }
        if (isset($content['meta']) && mb_strlen($content['meta']) > 320) {
            $errors['meta'] = 'Keep the meta description under 320 characters (currently '.mb_strlen($content['meta']).').';
        }

        foreach (Tree::strings($content) as $path => $value) {
            $key = str_contains($path, '.') ? substr($path, strrpos($path, '.') + 1) : $path;
            if (mb_strlen($value) > 20000) {
                $errors[$path] = 'This text is too long (maximum 20,000 characters).';
            } elseif (in_array($key, ['href', 'url', 'link_href', 'maps_url', 'whatsapp_url', 'linkedin'], true) && $value !== '' && ! preg_match('#^(/|\#|https?://|mailto:|tel:)#i', $value)) {
                $errors[$path] = 'Enter a site path starting with / or a full address starting with https://.';
            } elseif (in_array($key, ['email', 'questions_email'], true) && $value !== '' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$path] = 'Enter a valid email address.';
            } elseif (in_array($key, ['href', 'link_href'], true) && str_starts_with($value, '/') && ! self::pathExists($value)) {
                $errors[$path] = 'No page exists at '.$value.'. Check the address, or create the page first.';
            } elseif (preg_match_all('/\[[^\]]+\]\((\/[^)\s]*)\)/', $value, $links)) {
                $broken = array_filter($links[1], fn ($link) => ! self::pathExists($link));
                if ($broken) {
                    $errors[$path] = 'This text links to a page that does not exist: '.implode(', ', $broken).'.';
                }
            }
        }

        // The highlighted phrase must appear in the heading it highlights.
        foreach ($content['sections'] ?? [] as $index => $section) {
            if (is_array($section) && ($section['type'] ?? null) === 'hero') {
                if (trim((string) ($section['headline'] ?? '')) === '') {
                    $errors["sections.{$index}.headline"] = 'The homepage needs a main heading.';
                } elseif (($section['highlight'] ?? '') !== '' && ! str_contains($section['headline'], $section['highlight'])) {
                    $errors["sections.{$index}.highlight"] = 'This phrase does not appear in the main heading.';
                }
            }
        }

        if (isset($content['quote_form']['max_upload_kb']) && ($content['quote_form']['max_upload_kb'] < 1 || $content['quote_form']['max_upload_kb'] > 512000)) {
            $errors['quote_form.max_upload_kb'] = 'Enter a size between 1 and 512000 KB.';
        }

        return $errors;
    }
}
