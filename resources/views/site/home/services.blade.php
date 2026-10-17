@php($sectionId = $anchor)
<section class="nll-section nll-services" id="{{ $sectionId }}" aria-labelledby="{{ $sectionId }}-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                @if (!empty($section['eyebrow']))
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
                @endif
                <h2 id="{{ $sectionId }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
            </div>
            @if (!empty($section['aside']))
                <p class="nll-heading-aside">{{ $section['aside'] }}</p>
            @endif
        </div>

        <div class="nll-service-grid">
            @foreach ($section['items'] ?? [] as $service)
                @php($variant = in_array($service['style'] ?? '', ['localization', 'translation', 'audiovisual', 'data'], true) ? $service['style'] : 'data')
                <article class="nll-service nll-service-{{ $variant }}" data-reveal>
                    <div class="nll-service-top">
                        <span class="nll-service-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                @switch($variant)
                                    @case('localization')
                                        <circle cx="12" cy="12" r="8.5"/><path d="M3.8 9h16.4M3.8 15h16.4M12 3.6c2.1 2.3 3.1 5.1 3.1 8.4s-1 6.1-3.1 8.4c-2.1-2.3-3.1-5.1-3.1-8.4S9.9 5.9 12 3.6Z"/>
                                        @break
                                    @case('translation')
                                        <path d="M6 3.75h8l4.25 4.3v12.2H6z"/><path d="M14 3.9v4.5h4M9 12h6M9 15.5h6"/>
                                        @break
                                    @case('audiovisual')
                                        <rect x="3.5" y="5.25" width="17" height="13.5" rx="2"/><path d="m10 9 5 3-5 3V9Z"/>
                                        @break
                                    @default
                                        <path d="M4 13.5h2.5l2-6 3.5 11 3.5-13 2 8H20"/>
                                @endswitch
                            </svg>
                        </span>
                        @if (!empty($service['kicker']))
                            <span class="nll-service-kicker">{{ $service['kicker'] }}</span>
                        @endif
                    </div>

                    <h3 class="nll-service-title">{{ $service['title'] ?? '' }}</h3>
                    @if (!empty($service['body']))
                        <p class="nll-service-copy">{{ $service['body'] }}</p>
                    @endif

                    @if (!empty($service['deliverables']))
                        <ul class="nll-deliverables">
                            @foreach ($service['deliverables'] as $deliverable)
                                <li>
                                    @if (!empty($deliverable['href']))
                                        <a href="{{ $deliverable['href'] }}">
                                            <span class="nll-deliverable-title">{{ $deliverable['title'] ?? '' }}</span>
                                            <span class="nll-deliverable-body">{{ $deliverable['body'] ?? '' }}</span>
                                            <span class="nll-deliverable-link">{{ __('Learn more') }} <span class="nll-arrow" aria-hidden="true">→</span></span>
                                        </a>
                                    @else
                                        <span class="nll-deliverable-title">{{ $deliverable['title'] ?? '' }}</span>
                                        <span class="nll-deliverable-body">{{ $deliverable['body'] ?? '' }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if (!empty($service['link']['href']) && !empty($service['link']['label']))
                        <a class="nll-link" href="{{ $service['link']['href'] }}">{{ $service['link']['label'] }}<span class="visually-hidden">: {{ $service['title'] ?? '' }}</span> <span class="nll-arrow" aria-hidden="true">→</span></a>
                    @endif

                    @if ($variant === 'localization')
                        <svg class="nll-service-art" viewBox="0 0 220 120" fill="none" aria-hidden="true">
                            <rect x="20" y="16" width="120" height="84" rx="8"/><path d="M20 34h120M34 52h52M34 66h80M34 80h40"/>
                            <rect x="112" y="42" width="88" height="62" rx="8" class="nll-art-fill"/><path d="M126 62h44M126 76h60M126 90h30"/>
                        </svg>
                    @elseif ($variant === 'translation')
                        <svg class="nll-service-art" viewBox="0 0 220 120" fill="none" aria-hidden="true">
                            <path d="M60 14h64l22 22v76H60z"/><path d="M124 14v22h22M78 56h50M78 70h50M78 84h32"/>
                            <circle cx="168" cy="92" r="18" class="nll-art-fill"/><path d="m160 92 6 6 11-12"/>
                        </svg>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
