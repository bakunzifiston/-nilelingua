@props(['href' => '/', 'tone' => 'light', 'label' => null])

{{-- Full logo: mark + two-line wordmark. tone="dark" is for navy backgrounds. --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'nll-logo nll-logo-'.$tone]) }} aria-label="{{ $label ?? __('Nile Lingua Lab home') }}">
    <x-logo-mark/>
    <span class="nll-logo-text" aria-hidden="true">
        <span class="nll-logo-name">Nile Lingua</span>
        <span class="nll-logo-sub">Lab</span>
    </span>
</a>
