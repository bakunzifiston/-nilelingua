@extends('admin.layout')

@php use App\Support\Localization; @endphp

@section('title', $meta['label'])

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $meta['label'] }}</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Collection</span>
            <h1>{{ $meta['label'] }}</h1>
            <p>
                @if ($type === 'service')
                    Each service has its own page under /services/. To help visitors find a new service, link to it from the Services overview pillars, the main navigation or another page.
                @else
                    Articles are listed on the Knowledge Center page in the order shown here.
                @endif
            </p>
        </div>
        <a class="a-btn a-btn-primary" href="{{ route('admin.collections.create', $type) }}">@include('admin.partials.icon', ['name' => 'plus']) Add {{ $meta['singular'] }}</a>
    </div>

    <form class="a-filters" method="GET" action="{{ route('admin.collections.index', $type) }}" role="search">
        <div class="a-field a-search">
            <label class="a-label" for="q">Search</label>
            <input class="a-input" id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Title or address">
        </div>
        <div class="a-field">
            <label class="a-label" for="status">Status</label>
            <select class="a-select" id="status" name="status">
                <option value="">All</option>
                <option value="published" @selected($filters['status'] === 'published')>Published</option>
                <option value="draft" @selected($filters['status'] === 'draft')>Drafts &amp; unpublished</option>
                <option value="deleted" @selected($filters['status'] === 'deleted')>Deleted ({{ $trashedCount }})</option>
            </select>
        </div>
        <div class="a-field">
            <label class="a-label" for="lang">Language</label>
            <select class="a-select" id="lang" name="lang">
                <option value="">All languages</option>
                @foreach (Localization::LOCALES as $code => $language)
                    <option value="{{ $code }}" @selected($filters['lang'] === $code)>{{ $language['name'] }}</option>
                @endforeach
            </select>
        </div>
        <button class="a-btn" type="submit">Filter</button>
        @if ($filters['q'] !== '' || $filters['status'] !== '' || $filters['lang'] !== '')
            <a class="a-btn a-btn-quiet" href="{{ route('admin.collections.index', $type) }}">Clear</a>
        @endif
    </form>

    <section class="a-card">
        @if ($items->isEmpty())
            <div class="a-empty">
                @if ($filters['status'] === 'deleted')
                    <strong>The bin is empty</strong>Deleted {{ $meta['singular'] }}s appear here and can be restored.
                @elseif ($filters['q'] !== '' || $filters['status'] !== '')
                    <strong>Nothing matches these filters</strong>Try another search or clear the filters.
                @else
                    <strong>No {{ $meta['singular'] }}s yet</strong>Use “Add {{ $meta['singular'] }}” to create the first one.
                @endif
            </div>
        @else
            <div class="a-table-wrap">
                <table class="a-table">
                    <thead>
                        <tr>
                            @if ($canReorder)<th scope="col"><span class="visually-hidden">Order</span></th>@endif
                            <th scope="col">Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">French</th>
                            <th scope="col">Updated</th>
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody @if ($canReorder) data-sortable-rows data-reorder-url="{{ route('admin.collections.reorder', $type) }}" @endif>
                        @foreach ($items as $item)
                            @php($entry = $item['entry'])
                            <tr data-id="{{ $entry->id }}">
                                @if ($canReorder)
                                    <td style="width: 112px">
                                        <span class="a-item-tools">
                                            <span class="a-handle" data-handle title="Drag to reorder" aria-hidden="true">@include('admin.partials.icon', ['name' => 'drag'])</span>
                                            <button class="a-btn a-btn-icon" type="button" data-row-move="up" title="Move up">@include('admin.partials.icon', ['name' => 'up'])<span class="visually-hidden">Move {{ $item['title'] }} up</span></button>
                                            <button class="a-btn a-btn-icon" type="button" data-row-move="down" title="Move down">@include('admin.partials.icon', ['name' => 'down'])<span class="visually-hidden">Move {{ $item['title'] }} down</span></button>
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    @if ($entry->trashed())
                                        <strong>{{ $item['title'] }}</strong>
                                    @else
                                        <a class="a-row-title" href="{{ route('admin.entries.edit', $entry) }}">{{ $item['title'] }}</a>
                                    @endif
                                    <span class="a-row-sub">{{ $meta['url'] }}{{ $entry->slug }}@if ($item['summary']) · {{ $item['summary'] }}@endif</span>
                                    @if (!$entry->trashed() && $type === 'service' && empty($item['linked']))
                                        <span class="a-badge a-badge-warn" style="margin-top: 4px" title="No page or menu links to this service yet">Not linked from any page</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="a-states">
                                        @foreach ($item['filtered_states'] as $code => $state)
                                            <span class="a-badge {{ ['published' => 'a-badge-live', 'changes' => 'a-badge-draft', 'unpublished' => 'a-badge-off', 'missing' => 'a-badge-off'][$state] }}">
                                                {{ strtoupper($code) }} · {{ ['published' => 'Live', 'changes' => 'Changes', 'unpublished' => 'Draft', 'missing' => 'Empty'][$state] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    @if ($item['gaps']['missing'] || $item['gaps']['unpublished'])
                                        <span class="a-badge a-badge-warn">{{ $item['gaps']['missing'] ? $item['gaps']['missing'].' missing' : 'Not published' }}</span>
                                    @else
                                        <span class="a-badge a-badge-live">Complete</span>
                                    @endif
                                </td>
                                <td><span title="{{ $item['updated_at']?->format('j M Y, H:i') }}">{{ $item['updated_at']?->diffForHumans() }}</span></td>
                                <td>
                                    <div class="a-row-actions">
                                        @if ($entry->trashed())
                                            <form method="POST" action="{{ route('admin.entries.restore', $entry) }}">
                                                @csrf
                                                <button class="a-btn a-btn-sm" type="submit">@include('admin.partials.icon', ['name' => 'restore']) Restore</button>
                                            </form>
                                        @else
                                            <a class="a-btn a-btn-sm" href="{{ route('admin.entries.edit', $entry) }}">Edit</a>
                                            <a class="a-btn a-btn-sm a-btn-quiet" href="{{ route('admin.entries.preview', $entry) }}" target="_blank" rel="noopener">Preview</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('admin.partials.pagination', ['paginator' => $items])
        @endif
    </section>

    @if ($canReorder && $items->count() > 1)
        <p class="a-help" style="margin-top: 10px">Drag rows, or use the arrows, to change the order. The new order is saved automatically.</p>
    @elseif (!$items->isEmpty() && !$canReorder)
        <p class="a-help" style="margin-top: 10px">Clear the search and filters to reorder.</p>
    @endif
@endsection
