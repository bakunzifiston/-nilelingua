@extends('layouts.site')

@section('content')
    <section class="thank-you wrap">
        <div class="thank-you-mark" aria-hidden="true">✳</div>
        <span class="eyebrow">Message received</span>
        <h1>Thank you for<br><em>getting in touch.</em></h1>
        <p>Your enquiry has been submitted. We appreciate you sharing a little about your project.</p>
        @if (!session('enquiry_submitted'))
            <p class="thank-you-note">You can also use the contact form to send us your project details.</p>
        @endif
        <a class="button" href="/contact">Back to contact <span aria-hidden="true">↗</span></a>
    </section>
@endsection
