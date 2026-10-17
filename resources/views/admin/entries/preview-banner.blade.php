<div class="a-preview-banner" role="status">
    <style>
        .a-preview-banner { position: sticky; top: 0; z-index: 1000; display: flex; padding: 10px 16px; flex-wrap: wrap; align-items: center; justify-content: center; gap: 8px 16px; color: #1B1408; background: #E0A84F; font: 600 14px/1.4 "Avenir Next", Avenir, "Segoe UI", system-ui, sans-serif; text-align: center; }
        .a-preview-banner a { color: #1B1408; font-weight: 700; }
        .a-preview-banner ~ .nll-header { top: 41px; }
    </style>
    <span>Preview · {{ $label }} ({{ strtoupper($locale) }}) — {{ $state }}</span>
    <a href="{{ route('admin.entries.edit', [$entry, 'lang' => $locale]) }}" data-no-localize>Back to the editor</a>
</div>
