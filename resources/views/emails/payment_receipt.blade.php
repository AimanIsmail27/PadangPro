<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>{{ $data['subject'] ?? 'Payment Receipt - PadangPro' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111; line-height:1.6;">
  <h2>Payment Receipt — PadangPro</h2>

  <p>Hello {{ $data['name'] ?? 'Customer' }},</p>
  <p>Thank you! Your payment has been received successfully.</p>

  <hr>

  <table cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%; max-width:650px;">
    <tr>
      <td style="border:1px solid #ddd;"><b>Type</b></td>
      <td style="border:1px solid #ddd;">{{ $data['type'] ?? '-' }}</td>
    </tr>
    <tr>
      <td style="border:1px solid #ddd;"><b>Reference</b></td>
      <td style="border:1px solid #ddd;">{{ $data['ref'] ?? '-' }}</td>
    </tr>
    <tr>
      <td style="border:1px solid #ddd;"><b>Amount Paid</b></td>
      <td style="border:1px solid #ddd;">RM {{ number_format($data['amount'] ?? 0, 2) }}</td>
    </tr>
    <tr>
      <td style="border:1px solid #ddd;"><b>Date</b></td>
      <td style="border:1px solid #ddd;">{{ $data['date'] ?? now()->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
      <td style="border:1px solid #ddd;"><b>Status</b></td>
      <td style="border:1px solid #ddd;">PAID</td>
    </tr>
  </table>

  @if(!empty($data['details']))
    <h3 style="margin-top:18px;">Details</h3>
    <table cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%; max-width:650px;">
      @foreach($data['details'] as $label => $value)
        <tr>
          <td style="border:1px solid #ddd;"><b>{{ $label }}</b></td>
          <td style="border:1px solid #ddd;">{{ $value }}</td>
        </tr>
      @endforeach
    </table>
  @endif

  <p style="margin-top:18px;">
    If you have any questions, just reply to this email.<br>
    — PadangPro Team
  </p>
</body>
</html>
