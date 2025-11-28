<!DOCTYPE html>
<html>
<head>
    <title>Welcome to PadangPro</title>
</head>
<body>
    <h2>Hello {{ $name }},</h2>
    <p>Congratulations! You have been registered as a {{ ucfirst($userType) }} in the PadangPro system.</p>
    <p>Please use your registered email to login. Your temporary password is: <strong>default123</strong></p>
    <p>Make sure to change your password after first login.</p>
    <p>Thank you,<br>PadangPro Team</p>
</body>
</html>
