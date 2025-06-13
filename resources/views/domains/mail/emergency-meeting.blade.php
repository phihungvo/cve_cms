<!DOCTYPE html>
<html>

<head>
    <title>Emergency Meeting</title>
</head>

<body>
    <h1>Dear {{ $data['name'] ?? 'User' }}!</h1>
    <p>An emergency meeting has been scheduled.</p>
    <p><strong>Time:</strong> {{ $data['time'] ?? 'TBD' }}<br>
        <strong>Date:</strong> {{ $data['date'] ?? 'TBD' }}<br>
        <strong>Location/Link:</strong> {{ $data['location'] ?? 'TBD' }}
    </p>
    <p>{{ $data['message'] ?? 'Your attendance is required. Please prepare accordingly.' }}</p>
</body>

</html>