@extends('admin.layout')

@section('title', 'Dashboard')

@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

@section('content')
    <div class="a-page-head">
        <div>
            <h1>{{ $greeting }}, {{ str(auth()->user()->name)->before(' ') }}</h1>
            <p>Choose a page to edit. Changes stay private until you publish them.</p>
        </div>
        <div class="a-head-actions">
            <a class="a-btn" href="{{ route('admin.collections.create', 'article') }}">@include('admin.partials.icon', ['name' => 'plus']) New article</a>
            <a class="a-btn a-btn-primary" href="{{ url('/') }}" target="_blank" rel="noopener">@include('admin.partials.icon', ['name' => 'external']) View website</a>
        </div>
    </div>

    {{-- One clear status line --}}
    @if ($draftCount)
        <a class="a-status a-status-draft" href="#attention-title">
            <span class="a-status-dot" aria-hidden="true"></span>
            <span><strong>{{ $draftCount }} {{ $draftCount === 1 ? 'change is' : 'changes are' }} waiting to be published.</strong> Visitors still see the previous version.</span>
            <span class="a-status-link">Review →</span>
        </a>
    @else
        <div class="a-status a-status-live">
            <span class="a-status-dot" aria-hidden="true"></span>
            <span><strong>Everything is published.</strong> The website shows your latest changes.</span>
        </div>
    @endif

    <div class="a-kpis">
        @foreach ([
            ['Pages', $stats['pages']['total'], $stats['pages']['published'].' published', 'page', route('admin.entries.edit', $pages->first()['entry'] ?? 1)],
            ['Services', $stats['services']['total'], $stats['services']['published'].' published', 'service', route('admin.collections.index', 'service')],
            ['Articles', $stats['articles']['total'], $stats['articles']['published'].' published', 'article', route('admin.collections.index', 'article')],
            ['Images', $stats['media'], 'in the media library', 'image', route('admin.media.index')],
        ] as [$kpiLabel, $value, $note, $icon, $url])
            <a class="a-kpi" href="{{ $url }}">
                <span class="a-kpi-icon">@include('admin.partials.icon', ['name' => $icon])</span>
                <span class="a-kpi-text">
                    <span class="a-kpi-value">{{ $value }}</span>
                    <span class="a-kpi-label">{{ $kpiLabel }}</span>
                    <span class="a-kpi-note">{{ $note }}</span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- Insights: charts drawn from live data --}}
    <section class="a-insights" aria-labelledby="insights-title">
        <div class="a-section-head">
            <h2 id="insights-title">Insights</h2>
            <p>Form submissions from the website and publishing activity in the dashboard.</p>
        </div>

        <div class="a-chart-grid">
            <section class="a-card a-chart-card a-chart-wide" aria-labelledby="chart-submissions-title">
                <div class="a-card-head">
                    <div>
                        <h3 id="chart-submissions-title">Website submissions</h3>
                        <p>Quote requests, linguist applications and newsletter sign-ups · last 6 months</p>
                    </div>
                    <span class="a-chart-total">{{ $chartSubmissions['total'] }}<small>total</small></span>
                </div>
                <div class="a-card-links">
                    <a href="{{ route('admin.submissions.index', 'quotes') }}">Quote requests →</a>
                    <a href="{{ route('admin.submissions.index', 'applications') }}">Applications →</a>
                    <a href="{{ route('admin.submissions.index', 'subscribers') }}">Subscribers →</a>
                </div>
                <div class="a-card-body">
                    @if ($chartSubmissions['total'] === 0)
                        <div class="a-chart-empty is-static">@include('admin.partials.icon', ['name' => 'form'])<strong>No submissions yet</strong>Quote requests, applications and newsletter sign-ups appear here as soon as visitors send them.</div>
                    @else
                        @include('admin.partials.bar-chart', ['chartId' => 'chart-submissions', 'description' => 'Website submissions per month'] + $chartSubmissions)
                    @endif
                </div>
            </section>

            <section class="a-card a-chart-card" aria-labelledby="chart-services-title">
                <div class="a-card-head">
                    <div>
                        <h3 id="chart-services-title">Quote requests by service</h3>
                        <p>Which services visitors ask about · all time</p>
                    </div>
                    <a class="a-chart-link" href="{{ route('admin.submissions.index', 'quotes') }}">View all →</a>
                </div>
                <div class="a-card-body">
                    @if (empty($chartServices))
                        <div class="a-chart-empty is-static">@include('admin.partials.icon', ['name' => 'service'])<strong>No quote requests yet</strong>The split by service appears after the first request from the contact form.</div>
                    @else
                        @include('admin.partials.pie-chart', ['chartId' => 'chart-services', 'segments' => $chartServices, 'centerLabel' => 'requests', 'description' => 'Quote requests by service'])
                    @endif
                </div>
            </section>

            <section class="a-card a-chart-card a-chart-wide" aria-labelledby="chart-activity-title">
                <div class="a-card-head">
                    <div>
                        <h3 id="chart-activity-title">Publishing activity</h3>
                        <p>Versions published per week, by language · last 8 weeks</p>
                    </div>
                    <span class="a-chart-total">{{ $chartActivity['total'] }}<small>published</small></span>
                </div>
                <div class="a-card-body">
                    @include('admin.partials.bar-chart', ['chartId' => 'chart-activity', 'description' => 'Versions published per week', 'unit' => 'versions'] + $chartActivity)
                </div>
            </section>

            <section class="a-card a-chart-card" aria-labelledby="chart-status-title">
                <div class="a-card-head">
                    <div>
                        <h3 id="chart-status-title">Content status</h3>
                        <p>Every page, service and article, in each language</p>
                    </div>
                </div>
                <div class="a-card-body">
                    @include('admin.partials.pie-chart', ['chartId' => 'chart-status', 'segments' => $chartStatus, 'centerLabel' => 'versions', 'description' => 'Publication status of all content'])
                </div>
            </section>
        </div>
    </section>

    <div class="a-dash">
        <div class="a-dash-main">
            <section class="a-card" aria-labelledby="pages-title">
                <div class="a-card-head">
                    <div>
                        <h2 id="pages-title">Website pages</h2>
                        <p>Open a page to edit its text in English or French.</p>
                    </div>
                </div>
                <div class="a-tiles">
                    @foreach ($pages as $page)
                        <a class="a-tile" href="{{ route('admin.entries.edit', $page['entry']) }}">
                            <span class="a-tile-icon">@include('admin.partials.icon', ['name' => $page['home'] ? 'home' : 'page'])</span>
                            <span class="a-tile-title">{{ $page['title'] }}</span>
                            @if ($page['draft'])
                                <span class="a-tile-flag" title="Unpublished changes"></span><span class="visually-hidden">(unpublished changes)</span>
                            @endif
                        </a>
                    @endforeach
                </div>
                <div class="a-card-foot">
                    <a href="{{ route('admin.collections.index', 'service') }}">Services</a>
                    <a href="{{ route('admin.collections.index', 'article') }}">Articles</a>
                    @if ($general)<a href="{{ route('admin.entries.edit', $general) }}">Contact details</a>@endif
                    <a href="{{ route('admin.strings.index') }}">Interface text</a>
                    <a href="{{ route('admin.media.index') }}">Media library</a>
                </div>
            </section>

            <section class="a-card" aria-labelledby="recent-title">
                <div class="a-card-head"><h2 id="recent-title">Recent activity</h2></div>
                @if ($recent->isEmpty())
                    <div class="a-empty"><strong>No activity yet</strong>Edits you save appear here.</div>
                @else
                    <ul class="a-list a-list-compact">
                        @foreach ($recent as $item)
                            <li>
                                <div class="a-list-main">
                                    <a href="{{ route('admin.entries.edit', $item['entry']) }}">{{ $item['title'] }}</a>
                                    <span class="a-list-meta">
                                        @foreach ($item['locales'] as $code)<span class="a-lang">{{ strtoupper($code) }}</span> @endforeach
                                        {{ $item['when']?->diffForHumans() }}{{ $item['editor'] ? ' · '.$item['editor'] : '' }}
                                    </span>
                                </div>
                                @if ($item['draft'])
                                    <span class="a-badge a-badge-draft">Draft</span>
                                @else
                                    <span class="a-badge a-badge-live">Live</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <div class="a-dash-side">
            <section class="a-card" aria-labelledby="attention-title">
                <div class="a-card-head">
                    <div>
                        <h2 id="attention-title">Needs attention</h2>
                        <p>Drafts to publish and missing French text.</p>
                    </div>
                    @if ($attention->isNotEmpty())<span class="a-count-pill">{{ $attention->count() }}</span>@endif
                </div>
                @if ($attention->isEmpty())
                    <div class="a-empty">@include('admin.partials.icon', ['name' => 'check'])<strong>All clear</strong>Nothing is waiting for you.</div>
                @else
                    <ul class="a-list a-list-compact">
                        @foreach ($attention->take(6) as $item)
                            <li>
                                <span class="a-list-mark {{ $item['kind'] === 'draft' ? 'is-draft' : 'is-translate' }}" aria-hidden="true"></span>
                                <div class="a-list-main">
                                    <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                    <span class="a-list-meta"><span class="a-lang">{{ strtoupper($item['locale']) }}</span> {{ $item['detail'] }}</span>
                                </div>
                                <a class="a-btn a-btn-sm" href="{{ $item['url'] }}">{{ $item['action'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    @if ($attention->count() > 6)
                        <p class="a-card-more">+ {{ $attention->count() - 6 }} more</p>
                    @endif
                @endif
            </section>

        </div>
    </div>
@endsection
