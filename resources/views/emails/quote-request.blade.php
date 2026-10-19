@php
    $rows = [
        'Name' => $item->name,
        'Email' => $item->email,
        'Phone / WhatsApp' => $item->phone,
        'Organization' => $item->organization,
        'Service' => $item->service,
        'Industry' => $item->industry,
        'Languages' => $item->languages ? implode(', ', $item->languages) : null,
        'Other language' => $item->other_language,
        'Estimated volume' => $item->volume,
        'Deadline' => $item->deadline,
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<body style="margin:0; padding:24px 12px; background:#F4F6F7; font-family:'Segoe UI', Arial, sans-serif; color:#0F1E24;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px; margin:0 auto; background:#FFFFFF; border:1px solid #E1E6E8; border-radius:14px;">
        <tr>
            <td style="padding:22px 26px; background:#0B1D26; border-radius:14px 14px 0 0; color:#FFFFFF;">
                <div style="font-size:12px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#E0A84F;">Nile Lingua Lab · Website</div>
                <div style="margin-top:6px; font-family:Georgia, serif; font-size:22px;">New quote request</div>
                <div style="margin-top:4px; font-size:13px; color:#B4C4C6;">Received {{ $item->created_at?->format('j M Y, H:i') }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:22px 26px 6px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                    @foreach ($rows as $label => $value)
                        @continue($value === null || $value === '')
                        <tr>
                            <td style="padding:7px 12px 7px 0; width:150px; color:#6B7C80; vertical-align:top;">{{ $label }}</td>
                            <td style="padding:7px 0; font-weight:600; vertical-align:top;">
                                @if ($label === 'Email')
                                    <a href="mailto:{{ $value }}" style="color:#0F5C52;">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 26px 4px;">
                <div style="font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6B7C80;">Project summary</div>
                <div style="margin-top:8px; padding:14px 16px; border-left:3px solid #E0A84F; background:#F8FAFA; font-size:14.5px; line-height:1.6; white-space:pre-line;">{{ $item->message }}</div>
            </td>
        </tr>
        @if ($item->attachment_name)
            <tr>
                <td style="padding:14px 26px 0; font-size:14px;">
                    <strong>File:</strong> {{ $item->attachment_name }}
                    — {{ $fileAttached ? 'attached to this email.' : 'too large to attach; download it from the dashboard.' }}
                </td>
            </tr>
        @endif
        <tr>
            <td style="padding:22px 26px 26px;">
                <a href="mailto:{{ $item->email }}" style="display:inline-block; padding:11px 18px; border-radius:9px; background:#0F5C52; color:#FFFFFF; font-weight:700; text-decoration:none;">Reply to {{ $item->name }}</a>
                <a href="{{ $adminUrl }}" style="display:inline-block; margin-left:8px; padding:11px 18px; border-radius:9px; border:1px solid #CBD3D6; color:#0F1E24; font-weight:700; text-decoration:none;">Open in dashboard</a>
                <p style="margin:16px 0 0; font-size:12.5px; color:#6B7C80;">Replying to this email goes straight to the client.</p>
            </td>
        </tr>
    </table>
</body>
</html>
