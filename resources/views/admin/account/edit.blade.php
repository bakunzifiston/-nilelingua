@extends('admin.layout')

@section('title', 'My account')

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span aria-current="page">My account</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Account</span>
            <h1>My account</h1>
            <p>Update your name, email address or password.</p>
        </div>
    </div>

    <form class="a-card" method="POST" action="{{ route('admin.account.update') }}" style="max-width: 640px" novalidate>
        @csrf
        @method('PUT')
        <div class="a-card-body">
            @foreach (['name' => ['Name', 'text', 'name'], 'email' => ['Email address', 'email', 'username']] as $field => [$fieldLabel, $type, $autocomplete])
                <div class="a-field @error($field) has-error @enderror">
                    <label class="a-label" for="{{ $field }}">{{ $fieldLabel }}</label>
                    <input class="a-input" id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, auth()->user()->{$field}) }}" required autocomplete="{{ $autocomplete }}">
                    @error($field)<p class="a-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div class="a-form-row" style="margin-top: 16px">
                <div class="a-field @error('password') has-error @enderror">
                    <label class="a-label" for="password">New password <span class="a-help">(optional)</span></label>
                    <input class="a-input" id="password" name="password" type="password" autocomplete="new-password">
                </div>
                <div class="a-field">
                    <label class="a-label" for="password_confirmation">Repeat new password</label>
                    <input class="a-input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                </div>
            </div>
            @error('password')<p class="a-error">{{ $message }}</p>@enderror
            <div class="a-field @error('current_password') has-error @enderror">
                <label class="a-label" for="current_password">Current password</label>
                <input class="a-input" id="current_password" name="current_password" type="password" required autocomplete="current-password" aria-describedby="current-help">
                <p class="a-help" id="current-help">Required to save any change.</p>
                @error('current_password')<p class="a-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="a-actionbar" style="position: static; border-width: 1px 0 0; border-radius: 0 0 14px 14px; box-shadow: none">
            <span></span>
            <button class="a-btn a-btn-primary" type="submit">Save changes</button>
        </div>
    </form>
@endsection
