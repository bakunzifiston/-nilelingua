{{--
    One value of the content tree.
    $value, $key (last key), $name (form field name), $path (dot path), $pattern (path with * for list indexes),
    $depth (number of enclosing lists, for template placeholders), $schema, $reference (English content, French tab only).
--}}
@php
    $isList = is_array($value) && array_is_list($value);
    $fieldId = 'f_'.preg_replace('/[^A-Za-z0-9_]/', '_', $name);
@endphp

@if ($isList)
    @php
        $variants = $schema->variantsFor($pattern);
        $placeholder = '__i'.$depth.'__';
        $label = $schema->label((string) $key);
    @endphp
    <div class="a-repeater" data-repeater data-placeholder="{{ $placeholder }}">
        <div class="a-repeater-head">
            <h3>{{ $label }}</h3>
            <span data-count-label>{{ count($value) }} {{ count($value) === 1 ? 'item' : 'items' }}@if (count($value) > 1) · drag or use the arrows to reorder @endif</span>
        </div>
        @error('c.'.$path)<p class="a-error">{{ $message }}</p>@enderror
        <ol class="a-items" data-items>
            @foreach ($value as $index => $item)
                @include('admin.entries.item', [
                    'item' => $item,
                    'itemName' => $name.'['.$index.']',
                    'itemPath' => ($path === '' ? '' : $path.'.').$index,
                    'listKey' => $key,
                    'listPattern' => $pattern,
                    'itemPattern' => $schema->itemPattern($pattern, $item),
                    'isNew' => false,
                ])
            @endforeach
        </ol>
        @if (empty($value))
            <p class="a-help" data-empty-note>No items yet.@if ($variants) Use the button below to add one.@endif</p>
        @endif

        @foreach ($variants as $signature => $variantLabel)
            <template data-template="{{ $signature }}">
                @include('admin.entries.item', [
                    'item' => $schema->template($pattern, $signature),
                    'itemName' => $name.'['.$placeholder.']',
                    'itemPath' => ($path === '' ? '' : $path.'.').$placeholder,
                    'listKey' => $key,
                    'listPattern' => $pattern,
                    'itemPattern' => $schema->itemPattern($pattern, $schema->template($pattern, $signature)),
                    'isNew' => true,
                ])
            </template>
        @endforeach
        @if ($variants)
            <div class="a-add">
                @foreach ($variants as $signature => $variantLabel)
                    <button class="a-btn a-btn-sm" type="button" data-add="{{ $signature }}">@include('admin.partials.icon', ['name' => 'plus']) Add {{ count($variants) > 1 ? mb_strtolower($variantLabel) : mb_strtolower(str($label)->singular()->toString()) }}</button>
                @endforeach
            </div>
        @endif
    </div>

@elseif (is_array($value))
    @php
        $children = array_filter(array_keys($value), fn ($child) => !$schema->isHidden((string) $child) && $child !== 'type');
    @endphp
    @if ($children && ($asItem ?? false))
        {{-- The fields of a list item sit directly in the item. --}}
        @foreach ($children as $child)
            @include('admin.entries.node', [
                'value' => $value[$child],
                'key' => $child,
                'name' => $name.'['.$child.']',
                'path' => ($path === '' ? '' : $path.'.').$child,
                'pattern' => ($pattern === '' ? '' : $pattern.'.').$child,
                'asItem' => false,
            ])
        @endforeach
    @elseif ($children)
        <fieldset class="a-group">
            <legend>{{ $schema->label((string) $key) }}</legend>
            @foreach ($children as $child)
                @include('admin.entries.node', [
                    'value' => $value[$child],
                    'key' => $child,
                    'name' => $name.'['.$child.']',
                    'path' => ($path === '' ? '' : $path.'.').$child,
                    'pattern' => ($pattern === '' ? '' : $pattern.'.').$child,
                    'asItem' => false,
                ])
            @endforeach
        </fieldset>
    @endif

@else
    @php
        $kind = $schema->kind((string) $key, $value);
        $error = $errors->first('c.'.$path);
        $locked = $schema->isLocked($path);
        $help = $schema->help((string) $key, $pattern);
        $ref = ($reference !== null && is_string($value)) ? data_get($reference, $path) : null;
        $limit = ['meta' => 160, 'seo_title' => 60][$key] ?? null;
        $describedBy = trim(($help ? $fieldId.'_help ' : '').($error ? $fieldId.'_error' : ''));
    @endphp
    <div class="a-field {{ $error ? 'has-error' : '' }}">
        @if ($kind === 'checkbox')
            <input type="hidden" name="{{ $name }}" value="0">
            <label class="a-check" for="{{ $fieldId }}">
                <input id="{{ $fieldId }}" type="checkbox" name="{{ $name }}" value="1" @checked($value) @if ($locked) disabled @endif @if ($key === 'visible') data-visibility @endif>
                {{ $schema->label((string) $key) }}
            </label>
        @else
            <label class="a-label" for="{{ $fieldId }}">
                {{ $schema->label((string) $key) }}
                @if ($locked)<span class="a-lock">@include('admin.partials.icon', ['name' => 'lock']) Shared with English</span>@endif
                @if ($limit)<span class="a-count" data-counter-for="{{ $fieldId }}" data-limit="{{ $limit }}"></span>@endif
            </label>
            @switch($kind)
                @case('textarea')
                    <textarea class="a-textarea" id="{{ $fieldId }}" name="{{ $name }}" rows="{{ min(12, max(3, (int) ceil(mb_strlen((string) $value) / 80) + substr_count((string) $value, "\n"))) }}"
                              @if ($locked) readonly @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @if ($error) aria-invalid="true" @endif data-autosize>{{ $value }}</textarea>
                    @break
                @case('number')
                    <input class="a-input" id="{{ $fieldId }}" type="number" name="{{ $name }}" value="{{ $value }}" min="0" step="1" @if ($locked) readonly @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
                    @break
                @case('icon')
                    <select class="a-select" id="{{ $fieldId }}" name="{{ $name }}" @if ($locked) disabled @endif>
                        @foreach ($schema::icons() as $icon)
                            <option value="{{ $icon }}" @selected($value === $icon)>{{ str($icon)->headline() }}</option>
                        @endforeach
                        @if ($value !== '' && !in_array($value, $schema::icons(), true))
                            <option value="{{ $value }}" selected>{{ $value }}</option>
                        @endif
                    </select>
                    @break
                @case('choice')
                    <select class="a-select" id="{{ $fieldId }}" name="{{ $name }}" @if ($locked) disabled @endif>
                        @foreach ($schema->choices((string) $key) as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected($value === $optionValue)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                    @break
                @case('media')
                    <div class="a-media-field">
                        <span class="a-media-thumb" data-media-thumb @if ($value) style="background-image: url('{{ asset($value) }}')" @endif></span>
                        <select class="a-select" id="{{ $fieldId }}" name="{{ $name }}" data-media-select data-base="{{ asset('') }}" @if ($locked) disabled @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
                            <option value="">No image</option>
                            @foreach ($schema->mediaOptions() as $mediaPath => $mediaName)
                                <option value="{{ $mediaPath }}" @selected($value === $mediaPath)>{{ $mediaName }}</option>
                            @endforeach
                            @if ($value && !array_key_exists($value, $schema->mediaOptions()))
                                <option value="{{ $value }}" selected>{{ basename($value) }}</option>
                            @endif
                        </select>
                    </div>
                    <p class="a-help">Upload new images in the <a href="{{ route('admin.media.index') }}" target="_blank" rel="noopener">media library</a>, then choose them here.</p>
                    @break
                @default
                    <input class="a-input" id="{{ $fieldId }}" type="text" name="{{ $name }}" value="{{ $value }}"
                           @if ($locked) readonly @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @if ($error) aria-invalid="true" @endif>
            @endswitch
        @endif
        @if ($locked && in_array($kind, ['icon', 'choice', 'media'], true))
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
        @if ($help)<p class="a-help" id="{{ $fieldId }}_help">{{ $help }}</p>@endif
        @if ($locked)<p class="a-help">Edit this on the English tab; it is used for every language.</p>@endif
        @if ($ref !== null && $ref !== '' && !$locked)
            <p class="a-ref"><b>ENGLISH</b> {{ $ref }}</p>
        @endif
        @if ($error)<p class="a-error" id="{{ $fieldId }}_error" role="alert">{{ $error }}</p>@endif
    </div>
@endif
