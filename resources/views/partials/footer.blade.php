@php
    $socialLinks = collect(config('site.social', []))->filter(fn ($profile) => !empty($profile['url']));
    $newsletterStatus = session('newsletter_status');
    $newsletterErrors = $errors->getBag('newsletter');
    $contact = config('site.contact_info', []) + ['address_line_1' => '', 'address_line_2' => '', 'maps_url' => '', 'phone' => '', 'whatsapp_url' => '', 'email' => '', 'hours_days' => '', 'hours_time' => ''];
@endphp

<footer class="nll-footer">
    <div class="nll-wrap">
        <section class="nll-newsletter" id="newsletter" aria-labelledby="newsletter-title">
            <div class="nll-newsletter-copy">
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ __('Newsletter') }}</span>
                <h2 id="newsletter-title">{{ __('Stay close to African language work.') }}</h2>
                <p>{{ __('Occasional insights on localization, AI language data and multilingual communication, straight to your inbox.') }}</p>
            </div>

            <form class="nll-newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                @csrf
                <div class="nll-newsletter-field">
                    <label class="visually-hidden" for="newsletter-email">{{ __('Email address') }}</label>
                    <input id="newsletter-email" name="email" type="email" autocomplete="email" required maxlength="255"
                           placeholder="{{ __('you@company.com') }}" value="{{ $newsletterErrors->any() ? old('email') : '' }}"
                           @if ($newsletterErrors->has('email')) aria-invalid="true" aria-describedby="newsletter-message" @endif>
                    <button class="nll-button nll-button-gold" type="submit">{{ __('Subscribe') }} <span class="nll-arrow" aria-hidden="true">→</span></button>
                </div>
                {{-- Honeypot for spam bots; hidden from people and assistive technology. --}}
                <div class="nll-newsletter-trap" aria-hidden="true">
                    <label for="newsletter-website">{{ __('Website') }}</label>
                    <input id="newsletter-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                @if ($newsletterErrors->has('email'))
                    <p class="nll-newsletter-message is-error" id="newsletter-message" role="alert">{{ $newsletterErrors->first('email') }}</p>
                @elseif ($newsletterStatus === 'subscribed')
                    <p class="nll-newsletter-message is-success" role="status">{{ __('Thank you for subscribing. You’re on the list.') }}</p>
                @elseif ($newsletterStatus === 'already')
                    <p class="nll-newsletter-message is-success" role="status">{{ __('You’re already subscribed. Thank you for staying in touch.') }}</p>
                @else
                    <p class="nll-newsletter-note">{{ __('By subscribing, you agree to receive occasional emails from Nile Lingua Lab. See our') }} <a href="/privacy-policy">{{ __('Privacy & Data Protection Policy') }}</a>.</p>
                @endif
            </form>
        </section>

        <div class="nll-footer-main">
            <div class="nll-footer-brand">
                <a class="nll-brand nll-brand-footer" href="/" aria-label="{{ __('Nile Lingua Lab home') }}">
                    <span class="nll-brand-mark" aria-hidden="true">n.</span>
                    <span>Nile Lingua Lab<span class="nll-brand-period">.</span></span>
                </a>
                <p>{{ __('African language services from Kigali, Rwanda.') }}</p>
                <span class="nll-footer-assurance">{{ __('Native-speaker review · Strict confidentiality') }}</span>

                @if ($socialLinks->isNotEmpty())
                    <ul class="nll-social" aria-label="{{ __('Nile Lingua Lab on social media') }}">
                        @foreach ($socialLinks as $network => $profile)
                            <li>
                                <a href="{{ $profile['url'] }}" target="_blank" rel="noopener" aria-label="{{ $profile['label'] }} {{ __('(opens in a new tab)') }}" title="{{ $profile['label'] }}">
                                    <x-icon :name="$network"/>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <nav class="nll-footer-column" aria-label="{{ __('Explore') }}">
                <h2>{{ __('Explore') }}</h2>
                <a href="/services">{{ __('Services') }}</a><a href="/languages">{{ __('Languages') }}</a>
                <a href="/technology">{{ __('Technology') }}</a><a href="/knowledge-center">{{ __('Knowledge Center') }}</a>
            </nav>
            <nav class="nll-footer-column" aria-label="{{ __('Company') }}">
                <h2>{{ __('Company') }}</h2>
                <a href="/about">{{ __('About') }}</a><a href="/industries">{{ __('Industries') }}</a>
                <a href="/careers">{{ __('Careers') }}</a><a href="/contact">{{ __('Contact') }}</a>
            </nav>
            <address class="nll-footer-contact">
                <h2>{{ __('Contact') }}</h2>
                @if (!empty($contact['address_line_1']) || !empty($contact['address_line_2']))
                    <a href="{{ $contact['maps_url'] ?: '#' }}" target="_blank" rel="noopener">{{ $contact['address_line_1'] }}@if (!empty($contact['address_line_1']) && !empty($contact['address_line_2'])),<br>@endif{{ $contact['address_line_2'] }}</a>
                @endif
                @if (!empty($contact['phone']))
                    <a href="{{ \App\Support\Cms\Markup::tel($contact['phone']) }}">{{ $contact['phone'] }}</a>
                @endif
                @if (!empty($contact['whatsapp_url']))
                    <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp <span aria-hidden="true">↗</span></a>
                @endif
                @if (!empty($contact['email']))
                    <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
                @endif
                @if (!empty($contact['hours_days']) || !empty($contact['hours_time']))
                    <p>{{ $contact['hours_days'] }}<br>{{ $contact['hours_time'] }}</p>
                @endif
            </address>
        </div>
        <div class="nll-footer-bottom">
            <span>© {{ date('Y') }} Nile Lingua Lab Ltd · {{ $contact['address_line_2'] ?: __('Kigali, Rwanda') }}</span>
            <nav aria-label="{{ __('Footer menu') }}"><a href="/faq">{{ __('FAQ') }}</a><a href="/privacy-policy">{{ __('Privacy & Data Protection Policy') }}</a>
                @if (auth()->user()?->is_admin)
                    <a href="{{ route('admin.dashboard') }}" data-no-localize>{{ __('Admin dashboard') }}</a>
                @else
                    <a href="{{ route('admin.login') }}" data-no-localize rel="nofollow">{{ __('Admin sign in') }}</a>
                @endif
            </nav>
        </div>
    </div>
</footer>
