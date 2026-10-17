@extends('layouts.site')

@section('body-class', 'nll-page nll-page-thank-you')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
@endsection

@section('content')
    <section class="nll-thanks" aria-labelledby="thanks-title">
        <div class="nll-wrap">
            <div class="nll-thanks-card">
                <span class="nll-thanks-mark" aria-hidden="true"><x-icon name="check"/></span>
                @if (session('application_submitted'))
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Application received') }}</span>
                    <h1 id="thanks-title">{!! __('Thank you for <em>applying.</em>') !!}</h1>
                    <p>{{ __('Our resource team reviews your CV, language pairs and experience within 5 to 7 business days. Suitable candidates are invited to a short, subject-specific sample task.') }}</p>
                @else
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Message received') }}</span>
                    <h1 id="thanks-title">{!! __('Thank you for <em>getting in touch.</em>') !!}</h1>
                    <p>{{ __('Your enquiry has been submitted. We appreciate you sharing a little about your project.') }}</p>
                @endif
                @if (!session('enquiry_submitted') && !session('application_submitted'))
                    <p class="nll-thanks-note">{{ __('You can also use the contact form to send us your project details.') }}</p>
                @endif
                <div class="nll-actions">
                    <a class="nll-button nll-button-primary" href="/contact">{{ __('Back to contact') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                    <a class="nll-button nll-button-outline" href="/knowledge-center">{{ __('Browse the Knowledge Center') }}</a>
                </div>
                <p class="nll-thanks-alt">{{ __('Need to reach us sooner?') }} <a href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }}</a> · <a href="{{ \App\Support\Cms\Markup::tel(config('site.contact_info.phone')) }}">{{ __('Call us') }}</a></p>
            </div>
        </div>
    </section>
@endsection
