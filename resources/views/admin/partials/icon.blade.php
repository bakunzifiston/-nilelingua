{{-- Dashboard line icons (24px grid, stroke = currentColor). --}}
<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    @switch($name)
        @case('dashboard')<rect x="3.5" y="3.5" width="7" height="8" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="14.5" width="7" height="6" rx="1.5"/>@break
        @case('home')<path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-4.5v-6h-5v6H5a1 1 0 0 1-1-1z"/>@break
        @case('page')<path d="M6 3.75h8l4.25 4.3v12.2H6z"/><path d="M14 3.9v4.5h4M9 12h6M9 15.5h6"/>@break
        @case('service')<path d="M4 7.5h16v12H4z"/><path d="M9 7.5V5.2A1.2 1.2 0 0 1 10.2 4h3.6A1.2 1.2 0 0 1 15 5.2v2.3M4 12.5h16"/>@break
        @case('article')<path d="M5 4.5h14v15H5z"/><path d="M8.5 8.5h7M8.5 12h7M8.5 15.5h4"/>@break
        @case('settings')<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>@break
        @case('menu')<path d="M4 7h16M4 12h16M4 17h16"/>@break
        @case('text')<path d="M5 6.5V5h14v1.5M12 5v14M9 19h6"/>@break
        @case('nav')<path d="M4 6h16M4 12h10M4 18h13"/>@break
        @case('form')<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>@break
        @case('image')<rect x="3.5" y="5" width="17" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 5-4.5 4 3.5 3-2.5 4 3.5"/>@break
        @case('users')<circle cx="9" cy="8.5" r="3.2"/><path d="M3.5 19c.7-3.2 2.8-5 5.5-5s4.8 1.8 5.5 5"/><path d="M15.5 5.6a3 3 0 0 1 0 5.8M17.5 14.3c1.6.6 2.6 2.2 3 4.7"/>@break
        @case('external')<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>@break
        @case('eye')<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>@break
        @case('plus')<path d="M12 5v14M5 12h14"/>@break
        @case('trash')<path d="M4.5 7h15M9.5 7V4.5h5V7M6.5 7l1 13h9l1-13M10 11v5.5M14 11v5.5"/>@break
        @case('up')<path d="m6 14 6-6 6 6"/>@break
        @case('down')<path d="m6 10 6 6 6-6"/>@break
        @case('drag')<circle cx="9" cy="6" r=".9"/><circle cx="15" cy="6" r=".9"/><circle cx="9" cy="12" r=".9"/><circle cx="15" cy="12" r=".9"/><circle cx="9" cy="18" r=".9"/><circle cx="15" cy="18" r=".9"/>@break
        @case('check')<path d="m5 12.5 4.5 4.5L19 7.5"/>@break
        @case('alert')<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5v.01"/>@break
        @case('info')<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.5v.01"/>@break
        @case('history')<path d="M3.5 12a8.5 8.5 0 1 0 2.5-6"/><path d="M3.5 4.5V9H8M12 8v4.5l3 2"/>@break
        @case('logout')<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 16l4-4-4-4M14 12H4"/>@break
        @case('globe')<circle cx="12" cy="12" r="8.5"/><path d="M3.8 9h16.4M3.8 15h16.4M12 3.6c2.1 2.3 3.1 5.1 3.1 8.4s-1 6.1-3.1 8.4c-2.1-2.3-3.1-5.1-3.1-8.4S9.9 5.9 12 3.6Z"/>@break
        @case('copy')<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/>@break
        @case('restore')<path d="M4 12a8 8 0 1 0 2.3-5.6"/><path d="M4 4.5v5h5"/>@break
        @case('lock')<rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>@break
        @case('user')<circle cx="12" cy="8.5" r="3.5"/><path d="M5 20c.8-3.8 3.6-6 7-6s6.2 2.2 7 6"/>@break
        @default<circle cx="12" cy="12" r="8"/>
    @endswitch
</svg>
