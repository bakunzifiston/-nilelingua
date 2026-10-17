<section class="nll-section nll-tools" id="{{ $anchor }}" aria-labelledby="{{ $anchor }}-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                @if (!empty($section['eyebrow']))
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
                @endif
                <h2 id="{{ $anchor }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
            </div>
            @if (!empty($section['aside']))
                <p class="nll-heading-aside">{{ $section['aside'] }}</p>
            @endif
        </div>

        <div class="nll-tools-grid">
            @foreach ($section['groups'] ?? [] as $group)
                <section class="nll-tool-group" aria-labelledby="{{ $anchor }}-group-{{ $loop->iteration }}" data-reveal>
                    <span class="nll-icon nll-tool-icon" aria-hidden="true"><x-icon :name="$group['icon'] ?? 'layers'"/></span>
                    <h3 id="{{ $anchor }}-group-{{ $loop->iteration }}">{{ $group['title'] ?? '' }}</h3>
                    @if (!empty($group['tools']))
                        <ul class="nll-tags">
                            @foreach ($group['tools'] as $tool)
                                <li>{{ $tool }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (!empty($group['formats']))
                        <ul class="nll-tags nll-tags-format" aria-label="{{ __('File formats') }}">
                            @foreach ($group['formats'] as $format)
                                <li>{{ $format }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>

        @if (!empty($section['note']))
            <p class="nll-tools-note"><span aria-hidden="true">✳</span>{{ $section['note'] }}</p>
        @endif
    </div>
</section>
