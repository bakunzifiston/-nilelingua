{{-- One item of a repeatable list (see node.blade.php). --}}
@php
    $removable = $schema->canRemove($listPattern, $item);
    $itemLabel = str($schema->label((string) $listKey))->singular()->lower()->toString();
    $itemId = 'f_'.preg_replace('/[^A-Za-z0-9_]/', '_', $itemName);
    $hasError = !$isNew && collect($errors->getMessages())->keys()->contains(fn ($errorKey) => str_starts_with($errorKey, 'c.'.$itemPath.'.') || $errorKey === 'c.'.$itemPath);
@endphp

@if (!is_array($item))
    {{-- Plain text item --}}
    @php $error = $errors->first('c.'.$itemPath); @endphp
    <li class="a-item" data-item>
        <div class="a-item-row">
            <span class="a-handle" data-handle title="Drag to reorder" aria-hidden="true">@include('admin.partials.icon', ['name' => 'drag'])</span>
            <label class="visually-hidden" for="{{ $itemId }}">{{ ucfirst($itemLabel) }}</label>
            @if (mb_strlen((string) $item) > 90)
                <textarea class="a-textarea" id="{{ $itemId }}" name="{{ $itemName }}" rows="3" data-autosize @if ($error) aria-invalid="true" @endif>{{ $item }}</textarea>
            @else
                <input class="a-input" id="{{ $itemId }}" type="text" name="{{ $itemName }}" value="{{ $item }}" @if ($error) aria-invalid="true" @endif>
            @endif
            @include('admin.entries.item-tools', ['removable' => true])
        </div>
        @if ($reference !== null && is_string(data_get($reference, $itemPath)) && data_get($reference, $itemPath) !== '')
            <p class="a-ref" style="margin: 0 12px 10px 42px"><b>ENGLISH</b> {{ data_get($reference, $itemPath) }}</p>
        @endif
        @if ($error)<p class="a-error" style="margin: 0 12px 10px 42px">{{ $error }}</p>@endif
    </li>
@else
    @php
        $isRow = array_is_list($item);
        $type = $isRow ? null : ($item['type'] ?? null);
        $summary = \App\Support\Cms\EditorSchema::summary($item);
        $hidden = !$isRow && array_key_exists('visible', $item) && !$item['visible'];
    @endphp
    <li class="a-item {{ $hidden ? 'is-hidden-section' : '' }}" data-item>
        <details @if ($isNew || $hasError) open @endif>
            <summary>
                <span class="a-handle" data-handle title="Drag to reorder" aria-hidden="true">@include('admin.partials.icon', ['name' => 'drag'])</span>
                @if ($type)<span class="a-type">{{ $schema->sectionTypeLabel($type) }}</span>@endif
                <span class="a-item-title" data-summary>{{ $summary !== '' ? $summary : '' }}@if ($summary === '')<em>{{ $isNew ? 'New '.$itemLabel : 'Untitled '.$itemLabel }}</em>@endif</span>
                @if ($hidden)<span class="a-badge a-badge-off" data-hidden-badge>Hidden</span>@endif
                @if ($hasError)<span class="a-badge a-badge-warn">Check</span>@endif
                @include('admin.entries.item-tools', ['removable' => $removable])
            </summary>
            <div class="a-item-body">
                @unless ($isRow)
                    <input type="hidden" name="{{ $itemName }}[__v]" value="{{ \App\Support\Cms\EditorSchema::signature(\App\Support\Cms\EditorSchema::blank($item)) }}">
                @endunless
                @include('admin.entries.node', [
                    'value' => $item,
                    'key' => $isRow ? 'cells' : $listKey,
                    'name' => $itemName,
                    'path' => $itemPath,
                    'pattern' => $itemPattern,
                    'depth' => $depth + 1,
                    'asItem' => true,
                ])
            </div>
        </details>
    </li>
@endif
