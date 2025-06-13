<!DOCTYPE html>
<html>

<head>
    <title>Registration Confirmation</title>
</head>

<body>
    <h1>Hi {{ $data['name'] ?? 'User' }}!</h1>
    <p>Thank you for registering with us. Your account details are:</p>
    <p><strong>Username:</strong> {{ $data['username'] ?? 'N/A' }}<br>
        <strong>Email:</strong> {{ $data['email'] ?? 'N/A' }}
    </p>
    <p>{{ $data['message'] ?? 'Please click the link below to verify your account:' }}</p>
    <p><a href="{{ $data['verification_link'] ?? '#' }}">Verify Account</a></p>
</body>

</html>