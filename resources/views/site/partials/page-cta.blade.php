{{-- Closing call to action for inner pages, using the page's own "cta" line. --}}
@if (!empty($page['cta']))
    <x-cta-band compact :title="e($page['cta'])">
        <div class="nll-actions">
            <a class="nll-button nll-button-gold" href="/contact">{{ __('Let’s talk') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            <a class="nll-button nll-button-ghost" href="/services">{{ __('Explore our services') }}</a>
        </div>
    </x-cta-band>
@endif
