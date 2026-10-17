@extends('admin.layout')

@section('title', $meta['label'])

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span>Submissions</span>
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $meta['label'] }}</span>
@endsection

@section('content')
    @php
        $hasFilters = $filters['q'] !== '' || $filters['option'] !== '' || $filters['status'] !== '' || $filters['from'] || $filters['to'];
    @endphp

    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Submissions</span>
            <h1>{{ $meta['label'] }}</h1>
            <p>
                @switch($type)
                    @case('quotes') Requests sent from the quote form on the Contact page. Attached files are private and can only be downloaded here. @break
                    @case('applications') Applications to join the linguist network, sent from the Careers page, with the applicant's CV. @break
                    @default People who subscribed to the newsletter in the website footer.
                @endswitch
            </p>
        </div>
        <div class="a-head-actions">
            <a class="a-btn" href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}">@include('admin.partials.icon', ['name' => 'copy']) Export CSV</a>
        </div>
    </div>

    <nav class="a-subtabs" aria-label="Submission types">
        @foreach ($types as $key => $typeMeta)
            <a href="{{ route('admin.submissions.index', $key) }}" @if ($key === $type) aria-current="page" @endif>
                {{ $typeMeta['label'] }}
                <span class="a-nav-count {{ $counts[$key]['new'] ? 'is-new' : '' }}">{{ $counts[$key]['new'] ? $counts[$key]['new'].' new' : $counts[$key]['total'] }}</span>
            </a>
        @endforeach
    </nav>

    <form class="a-filters" method="GET" action="{{ route('admin.submissions.index', $type) }}" role="search">
        <div class="a-field a-search">
            <label class="a-label" for="q">Search</label>
            <input class="a-input" id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ $type === 'subscribers' ? 'Email address' : 'Name, email, organization…' }}">
        </div>
        @if ($meta['filter'])
            <div class="a-field">
                <label class="a-label" for="option">{{ $type === 'quotes' ? 'Service' : 'Expertise' }}</label>
                <select class="a-select" id="option" name="option">
                    <option value="">All</option>
                    @foreach ($options as $option)
                        <option value="{{ $option }}" @selected($filters['option'] === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if ($meta['readable'])
            <div class="a-field">
                <label class="a-label" for="status">Status</label>
                <select class="a-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="new" @selected($filters['status'] === 'new')>New</option>
                    <option value="read" @selected($filters['status'] === 'read')>Read</option>
                </select>
            </div>
        @endif
        <div class="a-field">
            <label class="a-label" for="from">From</label>
            <input class="a-input" id="from" type="date" name="from" value="{{ $filters['from'] }}">
        </div>
        <div class="a-field">
            <label class="a-label" for="to">To</label>
            <input class="a-input" id="to" type="date" name="to" value="{{ $filters['to'] }}">
        </div>
        <button class="a-btn" type="submit">Filter</button>
        @if ($hasFilters)
            <a class="a-btn a-btn-quiet" href="{{ route('admin.submissions.index', $type) }}">Clear</a>
        @endif
    </form>

    <section class="a-card">
        @if ($items->isEmpty())
            <div class="a-empty">
                @if ($hasFilters)
                    <strong>Nothing matches these filters</strong>Try another search or clear the filters.
                @else
                    <strong>No {{ strtolower($meta['label']) }} yet</strong>They appear here as soon as someone sends the form on the website.
                @endif
            </div>
        @else
            <div class="a-table-wrap">
                <table class="a-table">
                    <thead>
                        <tr>
                            @if ($type === 'subscribers')
                                <th scope="col">Email</th>
                                <th scope="col">Subscribed</th>
                            @else
                                <th scope="col">From</th>
                                <th scope="col">{{ $type === 'quotes' ? 'Service' : 'Languages' }}</th>
                                <th scope="col">{{ $type === 'quotes' ? 'Message' : 'Expertise' }}</th>
                                <th scope="col">Received</th>
                            @endif
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            @if ($type === 'subscribers')
                                <tr>
                                    <td><a class="a-row-title" href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                                    <td><span title="{{ $item->created_at?->format('j M Y, H:i') }}">{{ $item->created_at?->format('j M Y') }}</span></td>
                                    <td>
                                        <div class="a-row-actions">
                                            <form method="POST" action="{{ route('admin.submissions.destroy', [$type, $item->id]) }}" data-confirm="Remove {{ $item->email }} from the newsletter list?" data-confirm-title="Remove subscriber?" data-confirm-button="Remove">
                                                @csrf
                                                @method('DELETE')
                                                <button class="a-btn a-btn-sm a-btn-danger" type="submit">Remove</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @php $url = route('admin.submissions.show', [$type, $item->id]); @endphp
                                <tr class="{{ $item->read_at ? '' : 'is-new' }}">
                                    <td>
                                        <a class="a-row-title" href="{{ $url }}">@unless ($item->read_at)<span class="a-new-dot" aria-hidden="true"></span><span class="visually-hidden">New: </span>@endunless{{ $item->name }}</a>
                                        <span class="a-row-sub">{{ $item->email }}@if ($type === 'quotes' && $item->organization) · {{ $item->organization }}@endif @if ($type === 'applications') · {{ $item->location }}@endif</span>
                                    </td>
                                    <td>
                                        @if ($type === 'quotes')
                                            {{ $item->service }}
                                            @if ($item->attachment_path)<span class="a-row-sub">📎 File attached</span>@endif
                                        @else
                                            {{ implode(', ', $item->native_languages ?? []) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($type === 'quotes')
                                            <span class="a-clip">{{ $item->message }}</span>
                                        @else
                                            {{ $item->expertise }}
                                        @endif
                                    </td>
                                    <td><span title="{{ $item->created_at?->format('j M Y, H:i') }}">{{ $item->created_at?->diffForHumans() }}</span></td>
                                    <td><div class="a-row-actions"><a class="a-btn a-btn-sm" href="{{ $url }}">Open</a></div></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('admin.partials.pagination', ['paginator' => $items])
        @endif
    </section>
@endsection
