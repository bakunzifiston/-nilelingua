@php
    $form = config('site.application_form');
    $selectedNative = old('native_languages', []);
    $selectedTools = old('tools', []);
    $maxMb = (int) round($form['max_upload_kb'] / 1024);
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <p class="nll-lead nll-lead-secondary">{{ $page['network_lead'] }} <strong>{{ $page['network_languages'] }}</strong>{{ $page['network_tail'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="#apply">{{ __('Apply to Join Our Network') }} <span class="nll-arrow" aria-hidden="true">↓</span></a>
    </div>
    <p class="nll-hero-disclaimer">{{ $page['disclaimer'] }}</p>

    <x-slot:aside>
        {{-- A network of language professionals around Nile Lingua Lab. --}}
        <div class="nll-network" aria-hidden="true">
            <svg class="nll-network-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M50 50 28 14M50 50 79 50M50 50 30 86"/>
            </svg>
            <span class="nll-network-hub">
                <span class="nll-network-pulse"></span>
                <span class="nll-brand-mark">n.</span>
            </span>
            <div class="nll-profile nll-profile-1">
                <span class="nll-profile-avatar"><x-icon name="document"/></span>
                <div>
                    <b>{{ __('Translators & Editors') }}</b>
                    <span class="nll-profile-tags"><i>{{ __('Legal') }}</i><i>{{ __('Development') }}</i></span>
                </div>
            </div>
            <div class="nll-profile nll-profile-2">
                <span class="nll-profile-avatar"><x-icon name="captions"/></span>
                <div>
                    <b>{{ __('Subtitlers & Transcribers') }}</b>
                    <span class="nll-profile-tags"><i>.srt</i><i>{{ __('Verbatim') }}</i></span>
                </div>
            </div>
            <div class="nll-profile nll-profile-3">
                <span class="nll-profile-avatar"><x-icon name="chip"/></span>
                <div>
                    <b>{{ __('AI Data & RLHF Evaluators') }}</b>
                    <span class="nll-profile-tags"><i>{{ __('Benchmarking') }}</i></span>
                </div>
            </div>
        </div>
    </x-slot:aside>
</x-page-hero>

{{-- Who We Work With --}}
<section class="nll-section" aria-labelledby="roles-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Four areas') }}</span>
                <h2 id="roles-title">{!! __('Who We <em>Work With</em>') !!}</h2>
            </div>
            <p class="nll-heading-aside">{{ $page['roles_intro'] }}</p>
        </div>
        <div class="nll-sector-grid">
            @foreach ($page['roles'] as $role)
                <article class="nll-sector nll-variant-teal" data-reveal>
                    <header class="nll-sector-head">
                        <span class="nll-icon"><x-icon :name="$role['icon']"/></span>
                        <h3>{{ $loop->iteration }}. {{ $role['title'] }}</h3>
                    </header>
                    <ul class="nll-sector-points">
                        <li><strong>{{ __('Scope:') }}</strong> {{ $role['scope'] }}</li>
                        <li><strong>{{ __('Requirements:') }}</strong> {{ $role['requirements'] }}</li>
                    </ul>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Minimum Requirements --}}
<section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="requirements-title">
    <div class="nll-wrap nll-story">
        <div class="nll-story-intro" data-reveal>
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Before you apply') }}</span>
            <h2 class="nll-h2" id="requirements-title">{!! __('Minimum <em>Requirements</em>') !!}</h2>
            <p class="nll-story-lead">{{ $page['requirements_intro'] }}</p>
        </div>
        <div class="nll-story-rows">
            @foreach ($page['requirements'] as $item)
                <article class="nll-story-row" data-reveal>
                    <span class="nll-icon nll-story-icon"><x-icon :name="$item['icon']"/></span>
                    <h3>{{ $item['label'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Apply to Join Our Network + What Happens After You Apply --}}
<section class="nll-section nll-anchor" id="apply" aria-labelledby="apply-title">
    <div class="nll-wrap nll-contact">
        <div class="nll-contact-main">
            <div class="nll-form-card">
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Application form') }}</span>
                <h2 class="nll-h2" id="apply-title">{!! __('Apply to Join Our <em>Network</em>') !!}</h2>
                <p class="nll-form-intro">{{ $page['apply_intro'] }}</p>

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

                <form class="nll-form" method="POST" action="{{ route('careers.apply') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="nll-form-row">
                        <div class="nll-field @error('name') has-error @enderror">
                            <label for="name">{{ __('Full name') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="120" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                            @error('name')<span class="nll-field-error" id="name-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('email') has-error @enderror">
                            <label for="email">{{ __('Professional email') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="255" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            @error('email')<span class="nll-field-error" id="email-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="nll-form-row">
                        <div class="nll-field @error('phone') has-error @enderror">
                            <label for="phone">{{ __('Phone / WhatsApp') }} <span class="nll-optional">{{ __('(optional)') }}</span></label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" maxlength="40" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                            @error('phone')<span class="nll-field-error" id="phone-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('location') has-error @enderror">
                            <label for="location">{{ __('Country and city of residence') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="location" name="location" type="text" value="{{ old('location') }}" required maxlength="160" placeholder="{{ __('e.g. Rwanda, Kigali') }}" @error('location') aria-invalid="true" aria-describedby="location-error" @enderror>
                            @error('location')<span class="nll-field-error" id="location-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <fieldset class="nll-field nll-checks @error('native_languages') has-error @enderror">
                        <legend>{{ __('Native language(s)') }} <span class="nll-required" aria-hidden="true">*</span></legend>
                        <div class="nll-check-grid">
                            @foreach ($form['native_languages'] as $language)
                                <label class="nll-check">
                                    <input type="checkbox" name="native_languages[]" value="{{ $language }}" @checked(in_array($language, $selectedNative, true))>
                                    <span>{{ __($language) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('native_languages')<span class="nll-field-error">{{ $message }}</span>@enderror
                    </fieldset>

                    <div class="nll-form-row">
                        <div class="nll-field @error('language_pairs') has-error @enderror">
                            <label for="language_pairs">{{ __('Working language pairs') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="language_pairs" name="language_pairs" type="text" value="{{ old('language_pairs') }}" required maxlength="500" placeholder="{{ __('e.g. English to Kinyarwanda, French to Swahili') }}" @error('language_pairs') aria-invalid="true" aria-describedby="pairs-error" @enderror>
                            @error('language_pairs')<span class="nll-field-error" id="pairs-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('expertise') has-error @enderror">
                            <label for="expertise">{{ __('Main area of expertise') }} <span class="nll-required" aria-hidden="true">*</span></label>
                            <div class="nll-select">
                                <select id="expertise" name="expertise" required @error('expertise') aria-invalid="true" aria-describedby="expertise-error" @enderror>
                                    <option value="">{{ __('Choose an area') }}</option>
                                    @foreach ($form['expertise'] as $area)
                                        <option value="{{ $area }}" @selected(old('expertise') === $area)>{{ __($area) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('expertise')<span class="nll-field-error" id="expertise-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <fieldset class="nll-field nll-checks">
                        <legend>{{ __('Tools you use') }}</legend>
                        <div class="nll-check-grid">
                            @foreach ($form['tools'] as $tool)
                                <label class="nll-check">
                                    <input type="checkbox" name="tools[]" value="{{ $tool }}" @checked(in_array($tool, $selectedTools, true))>
                                    <span>{{ __($tool) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="nll-field @error('cv') has-error @enderror">
                        <label for="cv">{{ __('Resume / CV and portfolio') }} <span class="nll-required" aria-hidden="true">*</span></label>
                        <input class="nll-file" id="cv" name="cv" type="file" accept=".pdf,application/pdf" required aria-describedby="cv-hint @error('cv') cv-error @enderror">
                        <span class="nll-field-hint" id="cv-hint">{{ __('PDF upload, maximum :size MB.', ['size' => $maxMb]) }}</span>
                        @error('cv')<span class="nll-field-error" id="cv-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Honeypot for spam bots; hidden from people and assistive technology. --}}
                    <div class="nll-newsletter-trap" aria-hidden="true">
                        <label for="apply-website">{{ __('Website') }}</label>
                        <input id="apply-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="nll-field @error('consent') has-error @enderror">
                        <label class="nll-check nll-consent">
                            <input type="checkbox" name="consent" value="1" required @checked(old('consent')) @error('consent') aria-invalid="true" aria-describedby="consent-error" @enderror>
                            <span>{{ __('I agree that Nile Lingua Lab may use my details to assess my application, as described in the') }} <a href="/privacy-policy">{{ __('Privacy Policy') }}</a>. <span class="nll-required" aria-hidden="true">*</span></span>
                        </label>
                        @error('consent')<span class="nll-field-error" id="consent-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="nll-form-submit">
                        <button class="nll-button nll-button-gold" type="submit">{{ __('Submit Application') }} <span class="nll-arrow" aria-hidden="true">→</span></button>
                        <p class="nll-form-note">{{ __('Fields marked') }} <span class="nll-required">*</span> {{ __('are required.') }}</p>
                    </div>
                </form>
            </div>
        </div>

        <aside class="nll-contact-aside" aria-label="{{ __('After you apply') }}">
            <div class="nll-next-card nll-next-steps">
                <h2>{{ __('What Happens After You Apply?') }}</h2>
                <ol>
                    @foreach ($page['after_steps'] as $step)
                        <li>
                            <span class="nll-next-num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div><strong>{{ $step['title'] }}:</strong> {{ $step['body'] }}</div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="nll-contact-card">
                <h2>{{ __('Questions About Joining Us?') }}</h2>
                <p class="nll-talk-copy">{{ __('For questions about freelance work or partnerships, email') }} <a class="nll-inline-gold" href="mailto:{{ $page['questions_email'] }}"><strong>{{ $page['questions_email'] }}</strong></a>{{ __('. Looking for translation services instead?') }} <a class="nll-inline-gold" href="/contact#quote-form">{{ __('Request a quote') }}</a>.</p>
                <a class="nll-button nll-button-gold" href="mailto:{{ $page['questions_email'] }}">{{ __('Email the talent team') }}</a>
                <p class="nll-cta-note nll-talk-note">{{ $page['cta_note'] }}</p>
            </div>
        </aside>
    </div>
</section>
