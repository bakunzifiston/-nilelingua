@php
    // Error pages render outside the normal page stack, so they choose their language from the address.
    $fr = request()->is('fr') || request()->is('fr/*');
    $text = $fr ? $messages['fr'] : $messages['en'];
    $home = $fr ? '/fr' : '/';
    $contact = $fr ? '/fr/contact' : '/contact';
@endphp
<!DOCTYPE html>
<html lang="{{ $fr ? 'fr' : 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $text['title'] }} | Nile Lingua Lab</title>
    @include('partials.favicons')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/site.css') }}">
    <style>
        .nll-error { display: grid; min-height: 100vh; padding: 32px 16px; place-items: center; color: var(--nll-on-dark); background: radial-gradient(800px 420px at 85% -10%, rgb(73 179 160 / 20%), transparent 60%), var(--nll-navy); }
        .nll-error-card { width: min(560px, 100%); text-align: center; }
        .nll-error-code { display: block; margin-bottom: 8px; color: var(--nll-gold); font-family: var(--serif); font-size: clamp(64px, 14vw, 120px); font-style: italic; line-height: 1; }
        .nll-error h1 { margin: 0 0 12px; font-family: var(--serif); font-size: clamp(28px, 5vw, 40px); font-weight: 600; }
        .nll-error p { margin: 0 auto 28px; max-width: 46ch; color: var(--nll-on-dark-muted); font-size: 17px; line-height: 1.6; }
        .nll-error .nll-actions { justify-content: center; }
        .nll-error-brand { display: inline-flex; margin-bottom: 36px; align-items: center; gap: 10px; color: var(--nll-on-dark); font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
    <main class="nll-error">
        <div class="nll-error-card">
            <x-logo class="nll-error-brand" :href="$home" tone="dark" :label="$fr ? 'Accueil Nile Lingua Lab' : 'Nile Lingua Lab home'"/>
            <span class="nll-error-code" aria-hidden="true">{{ $code }}</span>
            <h1>{{ $text['title'] }}</h1>
            <p>{{ $text['body'] }}</p>
            <div class="nll-actions">
                <a class="nll-button nll-button-gold" href="{{ $home }}">{{ $fr ? 'Retour à l’accueil' : 'Back to the homepage' }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                <a class="nll-button nll-button-ghost" href="{{ $contact }}">{{ $fr ? 'Nous contacter' : 'Contact us' }}</a>
            </div>
        </div>
    </main>
</body>
</html>
