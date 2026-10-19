<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Nile Lingua Lab admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body>
    <a class="a-skip" href="#admin-main">Skip to content</a>

    <div class="a-shell" data-shell>
        <aside class="a-sidebar" id="admin-sidebar" aria-label="Dashboard menu">
            <a class="a-brand" href="{{ route('admin.dashboard') }}">
                <x-logo-mark class="a-brand-mark"/>
                <span>Nile Lingua Lab<small>Content dashboard</small></span>
            </a>

            <nav class="a-nav" aria-label="Dashboard">
                @foreach ($adminNav as $group)
                    <div class="a-nav-group">
                        <span class="a-nav-title" id="nav-{{ $loop->index }}">{{ $group['title'] }}</span>
                        <div role="list" aria-labelledby="nav-{{ $loop->index }}">
                            @foreach ($group['items'] as $item)
                                <a role="listitem" href="{{ $item['url'] }}" @if ($item['active']) aria-current="page" @endif>
                                    @include('admin.partials.icon', ['name' => $item['icon']])
                                    <span class="a-nav-label">{{ $item['label'] }}</span>
                                    @if (!empty($item['draft']))
                                        <span class="a-nav-dot" title="Unpublished changes"></span><span class="visually-hidden">(unpublished changes)</span>
                                    @endif
                                    @isset($item['count'])
                                        <span class="a-nav-count {{ !empty($item['new']) ? 'is-new' : '' }}">{{ $item['count'] }}@if (!empty($item['new']))<span class="visually-hidden"> new</span>@endif</span>
                                    @endisset
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="a-sidebar-foot">
                <a href="{{ url('/') }}" target="_blank" rel="noopener">View website ↗</a>
            </div>
        </aside>

        <div class="a-main">
            <header class="a-topbar">
                <button class="a-menu-toggle" type="button" aria-controls="admin-sidebar" aria-expanded="false" data-menu-toggle>
                    @include('admin.partials.icon', ['name' => 'menu'])
                    <span class="visually-hidden">Open menu</span>
                </button>

                <nav class="a-crumbs" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    @hasSection('crumbs')
                        @yield('crumbs')
                    @endif
                </nav>

                <div class="a-topbar-actions">
                    <a class="a-btn a-btn-quiet a-btn-sm a-topbar-site" href="{{ url('/') }}" target="_blank" rel="noopener">@include('admin.partials.icon', ['name' => 'external']) View site</a>
                    <details class="a-user" data-dismissable>
                        <summary>
                            <span class="a-avatar" aria-hidden="true">{{ str(auth()->user()->name)->explode(' ')->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}</span>
                            <span class="a-user-name">{{ auth()->user()->name }}</span>
                            <span class="visually-hidden">Account menu</span>
                        </summary>
                        <div class="a-user-menu">
                            <div class="a-user-meta">Signed in as<br><strong>{{ auth()->user()->email }}</strong></div>
                            <a href="{{ route('admin.account.edit') }}">My account</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit">Sign out</button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>

            <main class="a-content" id="admin-main" tabindex="-1">
                @if (session('success'))
                    <div class="a-flash a-flash-success" role="status">@include('admin.partials.icon', ['name' => 'check'])<span>{{ session('success') }}</span><button type="button" data-dismiss aria-label="Dismiss">×</button></div>
                @endif
                @if (session('error'))
                    <div class="a-flash a-flash-error" role="alert">@include('admin.partials.icon', ['name' => 'alert'])<span>{{ session('error') }}</span><button type="button" data-dismiss aria-label="Dismiss">×</button></div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Shared confirmation dialog for destructive actions (see admin.js). --}}
    <dialog class="a-dialog" data-confirm-dialog aria-labelledby="confirm-title">
        <form method="dialog">
            <div class="a-dialog-body">
                <h2 id="confirm-title" data-confirm-title>Are you sure?</h2>
                <p data-confirm-text></p>
            </div>
            <div class="a-dialog-actions">
                <button class="a-btn" value="cancel" autofocus>Cancel</button>
                <button class="a-btn a-btn-danger" value="confirm" data-confirm-ok>Confirm</button>
            </div>
        </form>
    </dialog>
    <div class="a-toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
