@php
    $socialLinks = collect(config('site.social', []))->filter(fn ($profile) => !empty($profile['url']));
    $newsletterStatus = session('newsletter_status');
    $newsletterErrors = $errors->getBag('newsletter');
@endphp

<footer class="nll-footer">
    <div class="nll-wrap">
        <section class="nll-newsletter" id="newsletter" aria-labelledby="newsletter-title">
            <div class="nll-newsletter-copy">
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Newsletter</span>
                <h2 id="newsletter-title">Stay close to African language work.</h2>
                <p>Occasional insights on localization, AI language data and multilingual communication, straight to your inbox.</p>
            </div>

            <form class="nll-newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                @csrf
                <div class="nll-newsletter-field">
                    <label class="visually-hidden" for="newsletter-email">Email address</label>
                    <input id="newsletter-email" name="email" type="email" autocomplete="email" required maxlength="255"
                           placeholder="you@company.com" value="{{ $newsletterErrors->any() ? old('email') : '' }}"
                           @if ($newsletterErrors->has('email')) aria-invalid="true" aria-describedby="newsletter-message" @endif>
                    <button class="nll-button nll-button-gold" type="submit">Subscribe <span class="nll-arrow" aria-hidden="true">→</span></button>
                </div>
                {{-- Honeypot for spam bots; hidden from people and assistive technology. --}}
                <div class="nll-newsletter-trap" aria-hidden="true">
                    <label for="newsletter-website">Website</label>
                    <input id="newsletter-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                @if ($newsletterErrors->has('email'))
                    <p class="nll-newsletter-message is-error" id="newsletter-message" role="alert">{{ $newsletterErrors->first('email') }}</p>
                @elseif ($newsletterStatus === 'subscribed')
                    <p class="nll-newsletter-message is-success" role="status">Thank you for subscribing. You’re on the list.</p>
                @elseif ($newsletterStatus === 'already')
                    <p class="nll-newsletter-message is-success" role="status">You’re already subscribed. Thank you for staying in touch.</p>
                @else
                    <p class="nll-newsletter-note">By subscribing, you agree to receive occasional emails from Nile Lingua Lab. See our <a href="/privacy-policy">Privacy &amp; Data Protection Policy</a>.</p>
                @endif
            </form>
        </section>

        <div class="nll-footer-main">
            <div class="nll-footer-brand">
                <a class="nll-brand nll-brand-footer" href="/" aria-label="Nile Lingua Lab home">
                    <span class="nll-brand-mark" aria-hidden="true">n.</span>
                    <span>Nile Lingua Lab<span class="nll-brand-period">.</span></span>
                </a>
                <p>African language services from Kigali, Rwanda.</p>
                <span class="nll-footer-assurance">Native-speaker review · Strict confidentiality</span>

                @if ($socialLinks->isNotEmpty())
                    <ul class="nll-social" aria-label="Nile Lingua Lab on social media">
                        @foreach ($socialLinks as $network => $profile)
                            <li>
                                <a href="{{ $profile['url'] }}" target="_blank" rel="noopener" aria-label="{{ $profile['label'] }} (opens in a new tab)" title="{{ $profile['label'] }}">
                                    <x-icon :name="$network"/>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <nav class="nll-footer-column" aria-label="Explore">
                <h2>Explore</h2>
                <a href="/services">Services</a><a href="/languages">Languages</a>
                <a href="/technology">Technology</a><a href="/knowledge-center">Knowledge Center</a>
            </nav>
            <nav class="nll-footer-column" aria-label="Company">
                <h2>Company</h2>
                <a href="/about">About</a><a href="/industries">Industries</a>
                <a href="/careers">Careers</a><a href="/contact">Contact</a>
            </nav>
            <address class="nll-footer-contact">
                <h2>Contact</h2>
                <a href="https://maps.google.com/?q=Gatenga%2C+Kicukiro%2C+Kigali%2C+Rwanda" target="_blank" rel="noopener">Gatenga, Kicukiro,<br>Kigali, Rwanda</a>
                <a href="tel:+250788750971">+250 788 750 971</a>
                <a href="https://wa.me/250788750971" target="_blank" rel="noopener">WhatsApp <span aria-hidden="true">↗</span></a>
                <a href="mailto:contact@nilelingua.com">contact@nilelingua.com</a>
                <p>Monday to Friday<br>08:00 to 18:00 CAT</p>
            </address>
        </div>
        <div class="nll-footer-bottom">
            <span>© {{ date('Y') }} Nile Lingua Lab Ltd · Kigali, Rwanda</span>
            <nav aria-label="Footer menu"><a href="/faq">FAQ</a><a href="/privacy-policy">Privacy &amp; Data Protection Policy</a></nav>
        </div>
    </div>
</footer>
