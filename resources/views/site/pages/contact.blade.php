<x-page-hero compact :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
</x-page-hero>

<section class="nll-section nll-section-tight" aria-labelledby="contact-form-title">
    <div class="nll-wrap nll-contact">
        <div class="nll-contact-main">
            <div class="nll-form-card">
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>A few details to get us started</span>
                <h2 class="nll-h2" id="contact-form-title">Tell us a little <em>about your project.</em></h2>
                <p class="nll-form-intro">We’ll use your details to understand what you need and consider the next step. Please don’t include confidential documents or sensitive personal information.</p>

                @if ($errors->any())
                    <div class="nll-form-errors" role="alert" tabindex="-1">
                        <strong>Please check the highlighted fields.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="nll-form" method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="nll-form-row">
                        <div class="nll-field @error('name') has-error @enderror">
                            <label for="name">Your name <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="120" placeholder="Jane Smith"
                                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                            @error('name')<span class="nll-field-error" id="name-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('email') has-error @enderror">
                            <label for="email">Work email <span class="nll-required" aria-hidden="true">*</span></label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="255" placeholder="jane@company.com"
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            @error('email')<span class="nll-field-error" id="email-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="nll-form-row">
                        <div class="nll-field @error('organization') has-error @enderror">
                            <label for="organization">Organization</label>
                            <input id="organization" name="organization" type="text" autocomplete="organization" value="{{ old('organization') }}" maxlength="160" placeholder="Your company"
                                   @error('organization') aria-invalid="true" aria-describedby="organization-error" @enderror>
                            @error('organization')<span class="nll-field-error" id="organization-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="nll-field @error('service') has-error @enderror">
                            <label for="service">What can we help with?</label>
                            <div class="nll-select">
                                <select id="service" name="service" @error('service') aria-invalid="true" aria-describedby="service-error" @enderror>
                                    <option value="">Choose a service</option>
                                    @foreach (config('site.services') as $service)
                                        <option value="{{ $service['title'] }}" @selected(old('service') === $service['title'])>{{ $service['title'] }}</option>
                                    @endforeach
                                    <option value="Something else" @selected(old('service') === 'Something else')>Something else</option>
                                </select>
                            </div>
                            @error('service')<span class="nll-field-error" id="service-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="nll-field @error('message') has-error @enderror">
                        <label for="message">A little about your project <span class="nll-required" aria-hidden="true">*</span></label>
                        <textarea id="message" name="message" rows="6" required maxlength="5000" placeholder="Content, languages, timing—anything that will help us understand what you need."
                                  @error('message') aria-invalid="true" aria-describedby="message-error" @enderror>{{ old('message') }}</textarea>
                        @error('message')<span class="nll-field-error" id="message-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="nll-form-submit">
                        <button class="nll-button nll-button-gold" type="submit">Send your enquiry <span class="nll-arrow" aria-hidden="true">→</span></button>
                        <p class="nll-form-note">Fields marked <span class="nll-required">*</span> are required. By sending this form, you agree that we may use these details to respond to your enquiry.</p>
                    </div>
                </form>
            </div>
        </div>

        <aside class="nll-contact-aside" aria-label="Other ways to reach us">
            <div class="nll-contact-card">
                <h2>Reach us directly</h2>
                <ul class="nll-contact-list">
                    <li><span class="nll-icon"><x-icon name="phone"/></span><div><span>Telephone</span><a href="tel:+250788750971">+250 788 750 971</a></div></li>
                    <li><span class="nll-icon"><x-icon name="whatsapp"/></span><div><span>WhatsApp</span><a href="https://wa.me/250788750971" target="_blank" rel="noopener">+250 788 750 971 <span aria-hidden="true">↗</span></a></div></li>
                    <li><span class="nll-icon"><x-icon name="mail"/></span><div><span>Email</span><a href="mailto:contact@nilelingua.com">contact@nilelingua.com</a></div></li>
                    <li><span class="nll-icon"><x-icon name="pin"/></span><div><span>Office</span><a href="https://maps.google.com/?q=Gatenga%2C+Kicukiro%2C+Kigali%2C+Rwanda" target="_blank" rel="noopener">Gatenga, Kicukiro, Kigali, Rwanda</a></div></li>
                    <li><span class="nll-icon"><x-icon name="clock"/></span><div><span>Opening hours</span><p>Monday to Friday, 08:00 to 18:00 CAT</p></div></li>
                </ul>
            </div>

            @foreach ($page['sections'] ?? [] as $section)
                <div class="nll-next-card">
                    <h2>{{ $section['heading'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </div>
            @endforeach

            <div class="nll-reassure">
                <span aria-hidden="true">✳</span>
                <p>Not sure what to ask for yet?<br>That’s a perfectly good place to start.</p>
            </div>
        </aside>
    </div>
</section>
