@php
    $roleIcons = ['message', 'film', 'workflow'];
    $sections = $page['sections'] ?? [];
    $status = $sections[0] ?? null;
    $notes = array_slice($sections, 1);
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">Introduce yourself <span class="nll-arrow" aria-hidden="true">→</span></a>
    </div>

    <x-slot:aside>
        {{-- A network of language professionals around Nile Lingua Lab, one card per profile on this page. --}}
        <div class="nll-network" aria-hidden="true">
            <svg class="nll-network-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M50 50 28 14M50 50 79 50M50 50 30 86"/>
            </svg>

            <span class="nll-network-hub">
                <span class="nll-network-pulse"></span>
                <span class="nll-brand-mark">n.</span>
            </span>

            <div class="nll-profile nll-profile-1">
                <span class="nll-profile-avatar"><x-icon name="message"/></span>
                <div>
                    <b>Linguists &amp; reviewers</b>
                    <span class="nll-profile-tags"><i>Language pairs</i><i>Subject areas</i></span>
                </div>
            </div>
            <div class="nll-profile nll-profile-2">
                <span class="nll-profile-avatar"><x-icon name="film"/></span>
                <div>
                    <b>Media specialists</b>
                    <span class="nll-profile-tags"><i>Subtitling</i><i>Accessibility</i></span>
                </div>
            </div>
            <div class="nll-profile nll-profile-3">
                <span class="nll-profile-avatar"><x-icon name="workflow"/></span>
                <div>
                    <b>Project &amp; language operations</b>
                    <span class="nll-profile-tags"><i>Coordination</i></span>
                </div>
            </div>
        </div>
    </x-slot:aside>
</x-page-hero>

@if ($status)
    <div class="nll-wrap nll-notice-wrap" data-reveal>
        <div class="nll-status" role="status">
            <span class="nll-status-dot" aria-hidden="true"></span>
            <div>
                <h2>{{ $status['heading'] }}</h2>
                <p>{{ $status['body'] }}</p>
            </div>
            <a class="nll-button nll-button-outline" href="/contact">Go to the contact form</a>
        </div>
    </div>
@endif

@if (count($cards))
    <section class="nll-section nll-section-tight" aria-labelledby="roles-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Who we’d like to hear from</span>
                    <h2 id="roles-title">Profiles we <em>work with</em></h2>
                </div>
            </div>
            <div class="nll-principles">
                @foreach ($cards as $card)
                    <article class="nll-principle" data-reveal>
                        <span class="nll-icon"><x-icon :name="$roleIcons[$loop->index] ?? 'people'"/></span>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@foreach ($notes as $note)
    <div class="nll-wrap nll-notice-wrap nll-notice-wrap-end" data-reveal>
        <div class="nll-notice" role="note">
            <span class="nll-notice-icon" aria-hidden="true"><x-icon name="lock"/></span>
            <div><strong>{{ $note['heading'] }}</strong><p>{{ $note['body'] }}</p></div>
        </div>
    </div>
@endforeach

@include('site.partials.page-cta')
