@php
    use App\Support\Localization;
    $current = Localization::current();
    $variant = $variant ?? 'compact';
@endphp

{{-- Language selector. Each option links to the same page in that language. --}}
<div class="nll-lang-switch nll-lang-switch-{{ $variant }}" role="group" aria-label="{{ __('Choose a language') }}">
    @if ($variant === 'menu')
        <span class="nll-lang-switch-title" aria-hidden="true">{{ __('Language') }}</span>
    @endif
    @foreach (Localization::LOCALES as $code => $language)
        <a class="nll-lang-option {{ $code === $current ? 'is-current' : '' }}"
           href="{{ Localization::switchUrl($code) }}"
           hreflang="{{ $code }}" lang="{{ $code }}"
           data-no-localize
           @if ($code === $current) aria-current="true" @endif
           title="{{ $language['name'] }}">
            @if ($variant === 'menu')
                {{ $language['name'] }}
            @else
                <span aria-hidden="true">{{ $language['short'] }}</span><span class="visually-hidden">{{ $language['name'] }}</span>
            @endif
        </a>
    @endforeach
</div>
