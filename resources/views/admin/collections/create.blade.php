@extends('admin.layout')

@section('title', 'Add '.$meta['singular'])

@section('crumbs')
    <span aria-hidden="true">/</span>
    <a href="{{ route('admin.collections.index', $type) }}">{{ $meta['label'] }}</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">Add {{ $meta['singular'] }}</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">New {{ $meta['singular'] }}</span>
            <h1>Add {{ $meta['singular'] }}</h1>
            <p>The new {{ $meta['singular'] }} uses the same page layout as an existing one. It starts as an unpublished draft in English and French.</p>
        </div>
    </div>

    <form class="a-card" method="POST" action="{{ route('admin.collections.store', $type) }}" style="max-width: 760px" novalidate>
        @csrf
        <div class="a-card-body">
            <div class="a-field @error('title') has-error @enderror">
                <label class="a-label" for="title">Title</label>
                <input class="a-input" id="title" name="title" value="{{ old('title') }}" required maxlength="160" data-slug-source @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                @error('title')<p class="a-error" id="title-error">{{ $message }}</p>@enderror
            </div>
            <div class="a-field @error('slug') has-error @enderror">
                <label class="a-label" for="slug">Web address</label>
                <input class="a-input" id="slug" name="slug" value="{{ old('slug') }}" required maxlength="80" pattern="[a-z0-9]+(-[a-z0-9]+)*" data-slug-target aria-describedby="slug-help @error('slug') slug-error @enderror">
                <p class="a-help" id="slug-help">The page will be at <strong>{{ $meta['url'] }}<span data-slug-preview>{{ old('slug') ?: 'your-address' }}</span></strong>. Lowercase letters, numbers and hyphens only; it cannot be changed later.</p>
                @error('slug')<p class="a-error" id="slug-error">{{ $message }}</p>@enderror
            </div>
            <div class="a-field @error('source') has-error @enderror">
                <label class="a-label" for="source">Start from the layout of</label>
                <select class="a-select" id="source" name="source" required>
                    <option value="">Choose a {{ $meta['singular'] }}…</option>
                    @foreach ($sources as $id => $sourceLabel)
                        <option value="{{ $id }}" @selected((string) old('source') === (string) $id)>{{ $sourceLabel }}</option>
                    @endforeach
                </select>
                @error('source')<p class="a-error">{{ $message }}</p>@enderror
            </div>
            <div class="a-field">
                <label class="a-check"><input type="checkbox" name="copy_text" value="1" @checked(old('copy_text'))> Copy its text as a starting point (otherwise the fields start empty)</label>
            </div>
        </div>
        <div class="a-actionbar" style="position: static; border-width: 1px 0 0; border-radius: 0 0 14px 14px; box-shadow: none">
            <a class="a-btn a-btn-quiet" href="{{ route('admin.collections.index', $type) }}">Cancel</a>
            <button class="a-btn a-btn-primary" type="submit">Create draft</button>
        </div>
    </form>
@endsection
