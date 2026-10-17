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
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Message received</span>
                <h1 id="thanks-title">Thank you for <em>getting in touch.</em></h1>
                <p>Your enquiry has been submitted. We appreciate you sharing a little about your project.</p>
                @if (!session('enquiry_submitted'))
                    <p class="nll-thanks-note">You can also use the contact form to send us your project details.</p>
                @endif
                <div class="nll-actions">
                    <a class="nll-button nll-button-primary" href="/contact">Back to contact <span class="nll-arrow" aria-hidden="true">→</span></a>
                    <a class="nll-button nll-button-outline" href="/knowledge-center">Browse the Knowledge Center</a>
                </div>
                <p class="nll-thanks-alt">Need to reach us sooner? <a href="https://wa.me/250788750971" target="_blank" rel="noopener">WhatsApp +250 788 750 971</a> · <a href="tel:+250788750971">Call us</a></p>
            </div>
        </div>
    </section>
@endsection
