@php
    $form = config('site.quote_form');

    // Preselect the service from ?service=… (dropdown key or a service page slug), unless the form is being re-shown.
    $requested = request()->query('service');
    $requested = $form['service_aliases'][$requested] ?? $requested;
    $selectedService = old('service', array_key_exists((string) $requested, $form['services']) ? $requested : '');
    $selectedLanguages = old('languages', []);
    $maxMb = (int) round($form['max_upload_kb'] / 1024);
    $whatsapp = config('site.contact_info.whatsapp_url').'?text='.rawurlencode(__('Hello, I’d like a quote for…'));
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    @foreach ($page['paragraphs'] as $paragraph)
        <p class="nll-lead {{ $loop->first ? '' : 'nll-lead-secondary' }}">{{ $paragraph }}</p>
    @endforeach
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="#quote-form">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">↓</span></a>
        <a class="nll-button nll-button-ghost" href="#quote-form">{{ __('Book a Consultation') }}</a>
        <a class="nll-button nll-button-ghost" href="{{ $whatsapp }}" target="_blank" rel="noopener">{{ __('WhatsApp Us') }} <span aria-hidden="true">↗</span></a>
    </div>

    <x-slot:aside>
        {{-- A first conversation: the client shares the basics, we ask for a little more. --}}
        <div class="nll-chat" aria-hidden="true">
            <div class="nll-chat-bubble nll-chat-in">
                <span class="nll-chat-label">{{ __('Your project') }}</span>
                <span class="nll-chat-tags"><i>{{ __('Content') }}</i><i>{{ __('Languages') }}</i><i>{{ __('Deadline') }}</i></span>
            </div>
            <div class="nll-chat-bubble nll-chat-out">
                <span class="nll-chat-label"><span class="nll-brand-mark" aria-hidden="true"><x-logo-mark/></span> Nile Lingua Lab</span>
                <b>{{ __('Here is your clear quote.') }}</b>
            </div>
            <div class="nll-chat-bubble nll-chat-typing"><i></i><i></i><i></i></div>
            <div class="nll-chat-channels">
                <span><x-icon name="mail"/></span>
                <span><x-icon name="whatsapp"/></span>
                <span><x-icon name="phone"/></span>
            </div>
        </div>
    </x-slot:aside>
</x-page-hero>

{{-- Contact Details --}}
<section class="nll-section nll-section-tight" aria-labelledby="details-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Reach us directly') }}</span>
                <h2 id="details-title">{!! __('Contact <em>Details</em>') !!}</h2>
            </div>
        </div>
        <ul class="nll-detail-cards">
            @foreach ($page['details'] as $row)
                <li data-reveal>
                    @if (!empty($row['href']))
                        <a class="nll-detail-card is-link" href="{{ $row['href'] }}" @if (str_starts_with($row['href'], 'http')) target="_blank" rel="noopener" @endif>
                    @else
                        <div class="nll-detail-card">
                    @endif
                        <span class="nll-icon" aria-hidden="true"><x-icon :name="$row['icon']"/></span>
                        <span class="nll-detail-text">
                            <span class="nll-detail-label">{{ $row['label'] }}</span>
                            <span class="nll-detail-value">{{ $row['text'] }}</span>
                        </span>
                        @if (!empty($row['href']))
                            <span class="nll-detail-go" aria-hidden="true">{{ str_starts_with($row['href'], 'http') ? '↗' : '→' }}</span>
                        @endif
                    @if (!empty($row['href']))
                        </a>
                    @else
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- Request a Quote + What Happens Next --}}
<section class="nll-section nll-section-sand nll-anchor" id="quote-form" aria-labelledby="quote-title">
    <div class="nll-wrap nll-contact">
        <div class="nll-contact-main">
            <div class="nll-form-card">
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Quote request') }}</span>
                <h2 class="nll-h2" id="quote-title">{!! __('Request a <em>Quote</em>') !!}</h2>
                <p class="nll-form-intro">{{ $page['form_intro'] }}</p>

                @if ($errors->any())
                    <div class="nll-form-errors" role="alert" tabindex="-1">
                        <strong>{{ __('Please check the highlighted fields.') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="nll-form" method="POST" action="{{ route('contact.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="nll-form-row">
                        <div class="nll-field @error('name') has-error @enderror">
                            <label for="name">{{ __('Full name') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="120" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                            @error('name')<span class="nll-field-error" id="name-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('email') has-error @enderror">
                            <label for="email">{{ __('Work email') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="255" aria-describedby="email-hint @error('email') email-error @enderror" @error('email') aria-invalid="true" @enderror>
                            <span class="nll-field-hint" id="email-hint">{{ __('A work email is preferred.') }}</span>
                            @error('email')<span class="nll-field-error" id="email-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="nll-form-row">
                        <div class="nll-field @error('phone') has-error @enderror">
                            <label for="phone">{{ __('Phone / WhatsApp') }} <span class="nll-optional">{{ __('(optional)') }}</span></label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" maxlength="40" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                            @error('phone')<span class="nll-field-error" id="phone-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('organization') has-error @enderror">
                            <label for="organization">{{ __('Organization / company name') }}</label>
                            <input id="organization" name="organization" type="text" autocomplete="organization" value="{{ old('organization') }}" maxlength="160" @error('organization') aria-invalid="true" aria-describedby="organization-error" @enderror>
                            @error('organization')<span class="nll-field-error" id="organization-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="nll-form-row">
                        <div class="nll-field @error('service') has-error @enderror">
                            <label for="service">{{ __('Service needed') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <div class="nll-select">
                                <select id="service" name="service" required @error('service') aria-invalid="true" aria-describedby="service-error" @enderror>
                                    <option value="">{{ __('Choose a service') }}</option>
                                    @foreach ($form['services'] as $key => $label)
                                        <option value="{{ $key }}" @selected($selectedService === $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('service')<span class="nll-field-error" id="service-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('industry') has-error @enderror">
                            <label for="industry">{{ __('Industry') }} <span class="nll-optional">{{ __('(optional)') }}</span></label>
                            <div class="nll-select">
                                <select id="industry" name="industry" @error('industry') aria-invalid="true" aria-describedby="industry-error" @enderror>
                                    <option value="">{{ __('Choose an industry') }}</option>
                                    @foreach ($form['industries'] as $industry)
                                        <option value="{{ $industry }}" @selected(old('industry') === $industry)>{{ __($industry) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('industry')<span class="nll-field-error" id="industry-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <fieldset class="nll-field nll-checks @error('languages') has-error @enderror @error('languages.*') has-error @enderror">
                        <legend>{{ __('Languages needed') }}</legend>
                        <div class="nll-check-grid">
                            @foreach ($form['languages'] as $language)
                                <label class="nll-check">
                                    <input type="checkbox" name="languages[]" value="{{ $language }}" @checked(in_array($language, $selectedLanguages, true))>
                                    <span>{{ __($language) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="visually-hidden" for="other_language">{{ __('Other language') }}</label>
                        <input class="nll-check-other" id="other_language" name="other_language" type="text" value="{{ old('other_language') }}" maxlength="120" placeholder="{{ __('Other language (if not listed)') }}">
                        @error('languages.*')<span class="nll-field-error">{{ $message }}</span>@enderror
                    </fieldset>

                    <div class="nll-form-row">
                        <div class="nll-field @error('volume') has-error @enderror">
                            <label for="volume">{{ __('Estimated volume') }}</label>
                            <input id="volume" name="volume" type="text" value="{{ old('volume') }}" maxlength="160" placeholder="{{ __('e.g. 15,000 words, 120 minutes of video or 500 UI strings') }}" @error('volume') aria-invalid="true" aria-describedby="volume-error" @enderror>
                            @error('volume')<span class="nll-field-error" id="volume-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('deadline') has-error @enderror">
                            <label for="deadline">{{ __('Deadline') }}</label>
                            <input id="deadline" name="deadline" type="text" value="{{ old('deadline') }}" maxlength="120" placeholder="{{ __('e.g. 30 November 2026') }}" @error('deadline') aria-invalid="true" aria-describedby="deadline-error" @enderror>
                            @error('deadline')<span class="nll-field-error" id="deadline-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="nll-field @error('message') has-error @enderror">
                        <label for="message">{{ __('Project summary and file formats') }} <span class="nll-required" aria-hidden="true">*</span></label>
                        <textarea id="message" name="message" rows="6" required maxlength="5000" placeholder="{{ __('Content, audience, file formats (for example .json, .xliff, .srt, .pdf)…') }}" @error('message') aria-invalid="true" aria-describedby="message-error" @enderror>{{ old('message') }}</textarea>
                        @error('message')<span class="nll-field-error" id="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="nll-field @error('attachment') has-error @enderror">
                        <label for="attachment">{{ __('File upload') }} <span class="nll-optional">{{ __('(optional)') }}</span></label>
                        <input class="nll-file" id="attachment" name="attachment" type="file" accept="{{ collect($form['file_types'])->map(fn ($t) => '.'.$t)->implode(',') }}" aria-describedby="attachment-hint @error('attachment') attachment-error @enderror">
                        <span class="nll-field-hint" id="attachment-hint">{{ __('Secure upload, maximum :size MB. For larger files, tell us and we will share a transfer link.', ['size' => $maxMb]) }}</span>
                        @error('attachment')<span class="nll-field-error" id="attachment-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Honeypot for spam bots; hidden from people and assistive technology. --}}
                    <div class="nll-newsletter-trap" aria-hidden="true">
                        <label for="quote-website">{{ __('Website') }}</label>
                        <input id="quote-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="nll-field @error('consent') has-error @enderror">
                        <label class="nll-check nll-consent">
                            <input type="checkbox" name="consent" value="1" required @checked(old('consent')) @error('consent') aria-invalid="true" aria-describedby="consent-error" @enderror>
                            <span>{{ __('I agree that Nile Lingua Lab may use these details to respond to my request, as described in the') }} <a href="/privacy-policy">{{ __('Privacy Policy') }}</a>. <span class="nll-required" aria-hidden="true">*</span></span>
                        </label>
                        @error('consent')<span class="nll-field-error" id="consent-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="nll-form-submit">
                        <button class="nll-button nll-button-gold" type="submit">{{ __('Submit Quote Request') }} <span class="nll-arrow" aria-hidden="true">→</span></button>
                        <p class="nll-form-note">{{ __('Fields marked') }} <span class="nll-required">*</span> {{ __('are required.') }}</p>
                    </div>
                </form>
            </div>
        </div>

        <aside class="nll-contact-aside" aria-label="{{ __('What happens next') }}">
            <div class="nll-next-card nll-next-steps">
                <h2>{{ __('What Happens Next') }}</h2>
                <ol>
                    @foreach ($page['next_steps'] as $step)
                        <li>
                            <span class="nll-next-num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div><strong>{{ $step['title'] }}:</strong> {{ $step['body'] }}</div>
                        </li>
                    @endforeach
                </ol>
                <p class="nll-next-note">{{ $page['next_steps_note'] }}</p>
            </div>

            <div class="nll-contact-card">
                <h2>{{ __('Prefer to Talk First?') }}</h2>
                <p class="nll-talk-copy">{{ $page['talk_first'] }}</p>
                <a class="nll-button nll-button-gold" href="{{ $whatsapp }}" target="_blank" rel="noopener">{{ __('WhatsApp us') }} <span aria-hidden="true">↗</span></a>
                <a class="nll-quote-alt" href="mailto:{{ config('site.contact_info.email') }}">{{ config('site.contact_info.email') }}</a>
            </div>

            <nav class="nll-related-card" aria-label="{{ __('Related pages') }}">
                <span class="nll-related-title">{{ __('Related') }}</span>
                @foreach ($page['related_links'] as $link)
                    <a href="{{ $link['href'] }}">{{ $link['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                @endforeach
            </nav>
        </aside>
    </div>
</section>
