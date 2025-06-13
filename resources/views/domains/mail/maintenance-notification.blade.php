<!DOCTYPE html>
<html>

<head>
    <title>Maintenance Notification</title>
</head>

<body>
    <h1>Dear {{ $data['name'] ?? 'User' }}!</h1>
    <p>We will be performing scheduled maintenance on our system.</p>
    <p><strong>Maintenance Window:</strong> {{ $data['start_time'] ?? 'TBD' }} - {{ $data['end_time'] ?? 'TBD' }}<br>
        <strong>Date:</strong> {{ $data['date'] ?? 'TBD' }}
    </p>
    <p>{{ $data['message'] ?? 'The system may be unavailable during this period. We apologize for any inconvenience.' }}
    </p>
</body>

</html>