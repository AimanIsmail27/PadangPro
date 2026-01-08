<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $data['subject'] ?? 'Payment Receipt - PadangPro' }}</title>
</head>

<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, sans-serif; color:#111827; line-height:1.6;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f3f4f6; padding:24px 12px;">
    <tr>
      <td align="center">

        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:680px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(17,24,39,0.08);">
          
          <!-- Header -->
          <tr>
            <td style="padding:18px 22px; background:linear-gradient(135deg,#15803d,#064e3b); color:#ffffff;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="vertical-align:middle;">
                    <!-- Logo + Brand -->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td style="vertical-align:middle; padding-right:10px;">
                          <img
                            src="{{ secure_url('images/logoPadang.png') }}"
                            alt="PadangPro"
                            width="36"
                            height="36"
                            style="display:block; border-radius:10px; border:1px solid rgba(255,255,255,0.25);"
                          >
                        </td>
                        <td style="vertical-align:middle; font-size:20px; font-weight:700; letter-spacing:0.2px;">
                          PadangPro
                        </td>
                      </tr>
                    </table>
                  </td>

                  <td align="right" style="vertical-align:middle; font-size:12px; opacity:0.9;">
                    {{ $data['date'] ?? now()->format('Y-m-d H:i') }}
                  </td>
                </tr>

                <tr>
                  <td colspan="2" style="padding-top:8px; font-size:14px; opacity:0.95;">
                    Payment Receipt
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:22px 22px 8px 22px;">
              <p style="margin:0 0 10px 0; font-size:16px;">
                Hi <b>{{ $data['name'] ?? 'Customer' }}</b>,
              </p>
              <p style="margin:0 0 16px 0; color:#374151;">
                Thank you! Your payment has been received successfully.
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px 0;">
                <tr>
                  <td style="background:#dcfce7; color:#166534; font-size:12px; font-weight:700; padding:6px 10px; border-radius:999px; display:inline-block;">
                    ✅ PAID
                  </td>
                </tr>
              </table>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:14px;">
                <tr>
                  <td style="padding:16px 16px;">
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:0.6px;">
                      Amount Paid
                    </div>
                    <div style="font-size:26px; font-weight:800; margin-top:4px;">
                      RM {{ number_format($data['amount'] ?? 0, 2) }}
                    </div>
                    <div style="font-size:13px; color:#6b7280; margin-top:2px;">
                      {{ $data['type'] ?? '-' }}
                    </div>
                  </td>
                </tr>
              </table>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:18px; border-collapse:separate; border-spacing:0 10px;">
                <tr>
                  <td style="width:34%; color:#6b7280; font-size:13px;">Reference</td>
                  <td style="color:#111827; font-size:13px; font-weight:700;">{{ $data['ref'] ?? '-' }}</td>
                </tr>
                <tr>
                  <td style="width:34%; color:#6b7280; font-size:13px;">Date</td>
                  <td style="color:#111827; font-size:13px; font-weight:700;">{{ $data['date'] ?? now()->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                  <td style="width:34%; color:#6b7280; font-size:13px;">Status</td>
                  <td style="color:#111827; font-size:13px; font-weight:700;">PAID</td>
                </tr>
              </table>

              @if(!empty($data['details']))
                <div style="margin-top:18px; padding-top:14px; border-top:1px solid #e5e7eb;">
                  <h3 style="margin:0 0 10px 0; font-size:16px;">Details</h3>

                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #e5e7eb; border-radius:14px; overflow:hidden;">
                    @foreach($data['details'] as $label => $value)
                      <tr>
                        <td style="padding:12px 14px; background:#f9fafb; border-bottom:1px solid #e5e7eb; width:38%; color:#6b7280; font-size:13px;">
                          {{ $label }}
                        </td>
                        <td style="padding:12px 14px; border-bottom:1px solid #e5e7eb; font-size:13px; color:#111827; font-weight:600;">
                          {{ $value }}
                        </td>
                      </tr>
                    @endforeach
                  </table>
                </div>
              @endif

              <p style="margin:18px 0 0 0; color:#374151; font-size:13px;">
                Need help? Just reply to this email and we’ll assist you.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:16px 22px; background:#f9fafb; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td>© {{ date('Y') }} PadangPro. All rights reserved.</td>
                  <td align="right" style="white-space:nowrap;">Receipt generated automatically</td>
                </tr>
              </table>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>
</html>
