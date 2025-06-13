<!DOCTYPE html>
<html>

<head>
    <title>Alert Notification</title>
</head>

<body>
    <h1>Dear {{ $data['name'] ?? 'User' }}!</h1>
    <p>This is an important alert regarding {{ $data['subject'] ?? 'an issue' }}.</p>
    <p><strong>Details:</strong> {{ $data['details'] ?? 'Please review immediately.' }}<br>
        <strong>Action Required:</strong> {{ $data['action'] ?? 'Take action now.' }}
    </p>
    <p>{{ $data['message'] ?? 'Contact support if you need assistance.' }}</p>
</body>

</html>