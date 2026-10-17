@php
    $hasPrimary = !empty($primary['label']) && !empty($primary['href']);
    $hasSecondary = !empty($secondary['label']) && !empty($secondary['href']);
@endphp
@if ($hasPrimary || $hasSecondary)
    <div class="nll-actions">
        @if ($hasPrimary)
            <a class="nll-button nll-button-gold" href="{{ $primary['href'] }}">{{ $primary['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        @endif
        @if ($hasSecondary)
            <a class="nll-button {{ $secondaryClass ?? 'nll-button-ghost' }}" href="{{ $secondary['href'] }}">{{ $secondary['label'] }}</a>
        @endif
    </div>
@endif
