@extends('admin.layout')

@section('title', $item->name)

@section('crumbs')
    <span aria-hidden="true">/</span>
    <a href="{{ route('admin.submissions.index', $type) }}">{{ $meta['label'] }}</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $item->name }}</span>
@endsection

@section('content')
    @php
        [$pathField, $nameField] = $meta['file'];
        $hasFile = $item->{$pathField} && \Illuminate\Support\Facades\Storage::disk('local')->exists($item->{$pathField});
        $rows = $type === 'quotes'
            ? [
                'Email' => $item->email,
                'Phone / WhatsApp' => $item->phone,
                'Organization' => $item->organization,
                'Service' => $item->service,
                'Industry' => $item->industry,
                'Languages' => $item->languages,
                'Other language' => $item->other_language,
                'Estimated volume' => $item->volume,
                'Deadline' => $item->deadline,
            ]
            : [
                'Email' => $item->email,
                'Phone / WhatsApp' => $item->phone,
                'Country & city' => $item->location,
                'Native languages' => $item->native_languages,
                'Language pairs' => $item->language_pairs,
                'Main expertise' => $item->expertise,
                'Tools' => $item->tools,
            ];
    @endphp

    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">{{ ucfirst($meta['singular']) }} · {{ $item->created_at?->format('j M Y, H:i') }}</span>
            <h1>{{ $item->name }}</h1>
            <p>{{ $type === 'quotes' ? ($item->service ?: 'Quote request') : $item->expertise }}</p>
        </div>
        <div class="a-head-actions">
            @if ($newer)<a class="a-btn" href="{{ route('admin.submissions.show', [$type, $newer]) }}">← Newer</a>@endif
            @if ($older)<a class="a-btn" href="{{ route('admin.submissions.show', [$type, $older]) }}">Older →</a>@endif
        </div>
    </div>

    <div class="a-detail">
        <div>
            <section class="a-card" aria-labelledby="details-title">
                <div class="a-card-head"><h2 id="details-title">Details</h2></div>
                <dl class="a-dl">
                    @foreach ($rows as $rowLabel => $value)
                        <dt>{{ $rowLabel }}</dt>
                        <dd>
                            @if (is_array($value))
                                @if ($value)
                                    <span class="a-chips">@foreach ($value as $chip)<span>{{ $chip }}</span>@endforeach</span>
                                @else
                                    <span class="a-help">—</span>
                                @endif
                            @elseif ($rowLabel === 'Email')
                                <a href="mailto:{{ $value }}">{{ $value }}</a>
                            @elseif ($rowLabel === 'Phone / WhatsApp' && $value)
                                <a href="{{ \App\Support\Cms\Markup::tel($value) }}">{{ $value }}</a>
                            @else
                                {{ $value ?: '—' }}
                            @endif
                        </dd>
                    @endforeach
                </dl>
            </section>

            @if ($type === 'quotes')
                <section class="a-card" aria-labelledby="message-title">
                    <div class="a-card-head"><h2 id="message-title">Project summary</h2></div>
                    <div class="a-message">{{ $item->message }}</div>
                </section>
            @endif
        </div>

        <aside class="a-editor-side" aria-label="Actions">
            <section class="a-side-card">
                <h2>Reply</h2>
                <a class="a-btn a-btn-primary" href="mailto:{{ $item->email }}?subject={{ rawurlencode($type === 'quotes' ? 'Your quote request – Nile Lingua Lab' : 'Your application – Nile Lingua Lab') }}">@include('admin.partials.icon', ['name' => 'external']) Email {{ str($item->name)->before(' ') }}</a>
                @if ($item->phone)
                    <a class="a-btn" href="https://wa.me/{{ preg_replace('/\D/', '', $item->phone) }}" target="_blank" rel="noopener">WhatsApp</a>
                @endif
            </section>

            <section class="a-side-card">
                <h2>{{ $type === 'quotes' ? 'Attached file' : 'CV & portfolio' }}</h2>
                @if ($hasFile)
                    <p>{{ $item->{$nameField} }}</p>
                    <a class="a-btn" href="{{ route('admin.submissions.download', [$type, $item->id]) }}">Download</a>
                @elseif ($item->{$pathField})
                    <p>The file “{{ $item->{$nameField} }}” is no longer available on the server.</p>
                @else
                    <p>No file was attached.</p>
                @endif
            </section>

            <section class="a-side-card">
                <h2>Record</h2>
                <dl>
                    <div><dt>Received</dt><dd>{{ $item->created_at?->format('j M Y, H:i') }}</dd></div>
                    <div><dt>Consent given</dt><dd>{{ $item->consented_at?->format('j M Y, H:i') ?? '—' }}</dd></div>
                </dl>
                <form method="POST" action="{{ route('admin.submissions.unread', [$type, $item->id]) }}" style="margin-top: 12px">
                    @csrf
                    <button class="a-btn" type="submit">Mark as unread</button>
                </form>
                <form method="POST" action="{{ route('admin.submissions.destroy', [$type, $item->id]) }}" data-confirm="Delete this {{ $meta['singular'] }} from {{ $item->name }}? Any attached file is deleted too. This cannot be undone." data-confirm-title="Delete {{ $meta['singular'] }}?" data-confirm-button="Delete">
                    @csrf
                    @method('DELETE')
                    <button class="a-btn a-btn-danger" type="submit">@include('admin.partials.icon', ['name' => 'trash']) Delete</button>
                </form>
            </section>
        </aside>
    </div>
@endsection
