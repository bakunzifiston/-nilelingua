@props(['name'])

{{-- Line icons drawn on a 24px grid; colour comes from currentColor. --}}
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" {{ $attributes }}>
    @switch($name)
        @case('globe')
            <circle cx="12" cy="12" r="8.5"/><path d="M3.8 9h16.4M3.8 15h16.4M12 3.6c2.1 2.3 3.1 5.1 3.1 8.4s-1 6.1-3.1 8.4c-2.1-2.3-3.1-5.1-3.1-8.4S9.9 5.9 12 3.6Z"/>
            @break
        @case('document')
            <path d="M6 3.75h8l4.25 4.3v12.2H6z"/><path d="M14 3.9v4.5h4M9 12h6M9 15.5h6"/>
            @break
        @case('play')
            <rect x="3.5" y="5.25" width="17" height="13.5" rx="2"/><path d="m10 9 5 3-5 3V9Z"/>
            @break
        @case('wave')
            <path d="M4 13.5h2.5l2-6 3.5 11 3.5-13 2 8H20"/>
            @break
        @case('captions')
            <rect x="3.5" y="5.25" width="17" height="13.5" rx="2"/><path d="M7 11.5h4M13 11.5h4M7 15h6M15 15h2"/>
            @break
        @case('mic')
            <rect x="9" y="3.5" width="6" height="11" rx="3"/><path d="M5.5 11.5a6.5 6.5 0 0 0 13 0M12 18v2.5"/>
            @break
        @case('chip')
            <rect x="6" y="6" width="12" height="12" rx="2"/><path d="M10 10h4v4h-4zM9 3v3M15 3v3M9 18v3M15 18v3M3 9h3M3 15h3M18 9h3M18 15h3"/>
            @break
        @case('shield')
            <path d="M12 3 19 6v5c0 4.5-2.8 7.9-7 10-4.2-2.1-7-5.5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('lock')
            <rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            @break
        @case('layers')
            <path d="m12 3.5 8.5 4.5-8.5 4.5L3.5 8 12 3.5Z"/><path d="m3.5 12 8.5 4.5 8.5-4.5M3.5 16 12 20.5l8.5-4.5"/>
            @break
        @case('workflow')
            <rect x="3" y="4" width="6" height="6" rx="1.5"/><rect x="15" y="14" width="6" height="6" rx="1.5"/><path d="M9 7h4a2 2 0 0 1 2 2v5"/>
            @break
        @case('check')
            <circle cx="12" cy="12" r="8.5"/><path d="m8.5 12 2.5 2.5 4.5-5"/>
            @break
        @case('cpu')
            <path d="M12 4a3 3 0 0 0-3 3 3 3 0 0 0-3 3c0 1 .5 2 1.2 2.5A3 3 0 0 0 9 17a3 3 0 0 0 3 3V4Z"/><path d="M12 4a3 3 0 0 1 3 3 3 3 0 0 1 3 3c0 1-.5 2-1.2 2.5A3 3 0 0 1 15 17a3 3 0 0 1-3 3"/>
            @break
        @case('scale')
            <path d="M12 4v16M7 20h10M5 7h14M5 7l-2.5 6a2.5 2.5 0 0 0 5 0L5 7ZM19 7l-2.5 6a2.5 2.5 0 0 0 5 0L19 7Z"/>
            @break
        @case('heart')
            <path d="M12 20s-7.5-4.4-7.5-10A4.3 4.3 0 0 1 12 7.6 4.3 4.3 0 0 1 19.5 10c0 5.6-7.5 10-7.5 10Z"/><path d="M8 12h2l1-2 2 4 1-2h2"/>
            @break
        @case('chart')
            <path d="M4 20V4M4 20h16"/><path d="m7.5 15 3.5-4 3 2.5 5-6"/>
            @break
        @case('film')
            <rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M7.5 4.5v15M16.5 4.5v15M3.5 9h4M3.5 15h4M16.5 9h4M16.5 15h4"/>
            @break
        @case('book')
            <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4H11v15H5.5A1.5 1.5 0 0 0 4 20.5v-15ZM20 5.5A1.5 1.5 0 0 0 18.5 4H13v15h5.5a1.5 1.5 0 0 1 1.5 1.5v-15Z"/>
            @break
        @case('people')
            <circle cx="9" cy="8.5" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><circle cx="16.5" cy="9.5" r="2.5"/><path d="M15.5 14.2A4.5 4.5 0 0 1 21 18.5"/>
            @break
        @case('compass')
            <circle cx="12" cy="12" r="8.5"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2Z"/>
            @break
        @case('message')
            <path d="M4 5.5h16v10H9l-5 4v-14Z"/><path d="M8 9.5h8M8 12.5h5"/>
            @break
        @case('pin')
            <path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/>
            @break
        @case('phone')
            <path d="M6.5 3.5h3l1.5 4.5-2 1.5a11 11 0 0 0 5.5 5.5l1.5-2 4.5 1.5v3a2 2 0 0 1-2 2A15.5 15.5 0 0 1 4.5 5.5a2 2 0 0 1 2-2Z"/>
            @break
        @case('mail')
            <rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 6.5 8 6.5 8-6.5"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>
            @break
        @case('whatsapp')
            <path d="M5 19.5 6 16a8 8 0 1 1 3 2.8l-4 .7Z"/><path d="M9.5 9c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 .8A4 4 0 0 1 11.2 11l.8-1-1-2-1.5 1Z"/>
            @break
        @case('sparkle')
            <path d="M12 3.5 13.8 10 20.5 12l-6.7 2-1.8 6.5-1.8-6.5L3.5 12l6.7-2L12 3.5Z"/>
            @break
        @default
            <circle cx="12" cy="12" r="8.5"/>
    @endswitch
</svg>
