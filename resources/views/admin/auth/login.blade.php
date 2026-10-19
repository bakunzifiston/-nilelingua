<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · Nile Lingua Lab admin</title>
    <link rel="icon" href="{{ \App\Support\VersionedAsset::url('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/admin.css') }}">
</head>
<body>
    <main class="a-auth">
        <div class="a-auth-card">
            <div class="a-brand">
                <x-logo-mark class="a-brand-mark"/>
                <span>Nile Lingua Lab<small>Content dashboard</small></span>
            </div>
            <h1>Sign in</h1>
            <p>For website administrators only.</p>

            @if (session('status'))
                <div class="a-flash a-flash-success" role="status"><span>{{ session('status') }}</span></div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" novalidate>
                @csrf
                <div class="a-field @error('email') has-error @enderror">
                    <label class="a-label" for="email">Email address</label>
                    <input class="a-input" id="email" name="email" type="email" autocomplete="username" required autofocus value="{{ old('email') }}"
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<p class="a-error" id="email-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="a-field @error('password') has-error @enderror">
                    <label class="a-label" for="password">Password</label>
                    <input class="a-input" id="password" name="password" type="password" autocomplete="current-password" required
                           @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    @error('password')<p class="a-error" id="password-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="a-field">
                    <label class="a-check"><input type="checkbox" name="remember" value="1"> Keep me signed in on this device</label>
                </div>
                <button class="a-btn a-btn-primary" type="submit">Sign in</button>
            </form>
            <p class="a-auth-foot"><a href="{{ url('/') }}">← Back to the website</a></p>
        </div>
    </main>
</body>
</html>
