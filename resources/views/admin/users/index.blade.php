@extends('admin.layout')

@section('title', 'Administrators')

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span aria-current="page">Administrators</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Access</span>
            <h1>Administrators</h1>
            <p>People who can sign in to this dashboard. There is no public sign-up: new administrators are added here.</p>
        </div>
    </div>

    <div class="a-grid-2">
        <section class="a-card" aria-labelledby="admins-title">
            <div class="a-card-head"><h2 id="admins-title">Current administrators</h2></div>
            <ul class="a-list">
                @foreach ($users as $user)
                    <li>
                        <div class="a-list-main">
                            <strong>{{ $user->name }}</strong>@if ($user->is(auth()->user())) <span class="a-badge a-badge-info a-badge-plain">You</span>@endif
                            <span class="a-list-meta">{{ $user->email }} · {{ $user->last_login_at ? 'Last signed in '.$user->last_login_at->diffForHumans() : 'Never signed in' }}</span>
                        </div>
                        @unless ($user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Remove dashboard access for {{ $user->name }}? Their past edits stay on record." data-confirm-title="Remove administrator?" data-confirm-button="Remove access">
                                @csrf
                                @method('DELETE')
                                <button class="a-btn a-btn-sm a-btn-danger" type="submit">Remove</button>
                            </form>
                        @endunless
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="a-card" aria-labelledby="add-title">
            <div class="a-card-head"><h2 id="add-title">Add an administrator</h2></div>
            <form class="a-card-body" method="POST" action="{{ route('admin.users.store') }}" novalidate>
                @csrf
                @php($bag = $errors->getBag('create'))
                <div class="a-field {{ $bag->has('name') ? 'has-error' : '' }}">
                    <label class="a-label" for="name">Name</label>
                    <input class="a-input" id="name" name="name" value="{{ $bag->any() ? old('name') : '' }}" required autocomplete="off">
                    @if ($bag->has('name'))<p class="a-error">{{ $bag->first('name') }}</p>@endif
                </div>
                <div class="a-field {{ $bag->has('email') ? 'has-error' : '' }}">
                    <label class="a-label" for="email">Email address</label>
                    <input class="a-input" id="email" name="email" type="email" value="{{ $bag->any() ? old('email') : '' }}" required autocomplete="off">
                    @if ($bag->has('email'))<p class="a-error">{{ $bag->first('email') }}</p>@endif
                </div>
                <div class="a-form-row" style="margin-top: 16px">
                    <div class="a-field {{ $bag->has('password') ? 'has-error' : '' }}">
                        <label class="a-label" for="password">Password</label>
                        <input class="a-input" id="password" name="password" type="password" required autocomplete="new-password" aria-describedby="password-help">
                    </div>
                    <div class="a-field">
                        <label class="a-label" for="password_confirmation">Repeat password</label>
                        <input class="a-input" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    </div>
                </div>
                <p class="a-help" id="password-help" style="margin-top: 6px">At least 10 characters, with letters and numbers.</p>
                @if ($bag->has('password'))<p class="a-error">{{ $bag->first('password') }}</p>@endif
                <button class="a-btn a-btn-primary" type="submit" style="margin-top: 16px">@include('admin.partials.icon', ['name' => 'plus']) Add administrator</button>
            </form>
        </section>
    </div>
@endsection
