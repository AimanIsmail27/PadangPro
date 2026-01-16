@php
    $isApproved = strtolower($statusLabel) === 'approved';
    $logoUrl = asset('images/logoPadang.png'); // make sure APP_URL is set so this becomes absolute in email
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PadangPro Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f7fb; padding:20px;">
    <div style="max-width:650px; margin:0 auto; background:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #e5e7eb;">
        <div style="background: linear-gradient(90deg, #4f46e5, #0f172a); padding:18px 20px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <img src="{{ $logoUrl }}" alt="PadangPro" style="height:38px; width:38px; border-radius:999px; background:#fff; padding:4px;">
                <div style="color:#fff;">
                    <div style="font-weight:800; font-size:18px; line-height:1;">PadangPro</div>
                    <div style="opacity:.85; font-size:12px; margin-top:4px;">Matchmaking Request Update</div>
                </div>
            </div>
        </div>

        <div style="padding:22px 20px; color:#0f172a;">
            <h2 style="margin:0 0 12px; font-size:18px;">
                Your request has been
                <span style="color: {{ $isApproved ? '#16a34a' : '#dc2626' }}; font-weight:800;">
                    {{ $statusLabel }}
                </span>
            </h2>

            <p style="margin:0 0 14px; color:#334155; line-height:1.6;">
                Hi {{ optional($application->customer)->customer_FullName ?? 'there' }},
                your request to join the advertisement below has been updated.
            </p>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin:14px 0;">
                <div style="font-weight:800; margin-bottom:6px;">{{ $ad->ads_Name ?? 'Advertisement' }}</div>
                <div style="font-size:13px; color:#475569; line-height:1.6;">
                    <div><strong>Type:</strong> {{ $ad->ads_Type ?? 'N/A' }}</div>
                    <div><strong>Slot:</strong>
                        {{ !empty($ad->ads_SlotTime) ? \Carbon\Carbon::parse($ad->ads_SlotTime)->format('D, M j | h:i A') : 'N/A' }}
                    </div>
                    <div><strong>Status:</strong> {{ $statusLabel }}</div>
                </div>
            </div>

            @if($isApproved)
                <p style="margin:0 0 12px; color:#334155; line-height:1.6;">
                    🎉 You’re approved! You can now contact the organizer in the app (WhatsApp button) and prepare for the match.
                </p>
            @else
                <p style="margin:0 0 12px; color:#334155; line-height:1.6;">
                    Your request was not approved this time. You can try applying to other matches in PadangPro.
                </p>
            @endif

            <p style="margin:16px 0 0; font-size:12px; color:#64748b;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
