<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - PadangPro</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #15803d;">PadangPro.</h2>
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" 
               style="background-color: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </div>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <hr style="border: 0; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #777;">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
        <p style="font-size: 12px; color: #777;">{{ route('password.reset', ['token' => $token, 'email' => $email]) }}</p>
    </div>
</body>
</html>