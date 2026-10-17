{{-- Custom text section added from the admin dashboard. --}}
@php($background = in_array($section['background'] ?? '', ['white', 'sand'], true) ? $section['background'] : 'white')
<section class="nll-section nll-home-text {{ $background === 'sand' ? 'is-sand' : '' }}" id="{{ $anchor }}" aria-labelledby="{{ $anchor }}-title">
    <div class="nll-wrap nll-home-text-inner" data-reveal>
        <div>
            @if (!empty($section['eyebrow']))
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
            @endif
            <h2 id="{{ $anchor }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
        </div>
        <div class="nll-home-text-body">
            @foreach ($section['paragraphs'] ?? [] as $paragraph)
                <p><x-rich :text="$paragraph"/></p>
            @endforeach
            @include('site.home.buttons', ['primary' => $section['primary_button'] ?? null, 'secondary' => $section['secondary_button'] ?? null, 'secondaryClass' => 'nll-button-outline'])
        </div>
    </div>
</section>
