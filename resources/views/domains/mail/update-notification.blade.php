<!DOCTYPE html>
<html>

<head>
    <title>Update Notification</title>
</head>

<body>
    <h1>Hello {{ $data['name'] ?? 'User' }}!</h1>
    <p>We are excited to inform you about an update to {{ $data['subject'] ?? 'our service' }}.</p>
    <p><strong>Details:</strong> {{ $data['details'] ?? 'Check out the new features.' }}</p>
    <p><strong>Action Required:</strong> {{ $data['action'] ?? 'No action needed.' }}</p>
    <p>Thank you for using our platform!</p>
</body>

</html>