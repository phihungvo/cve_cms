<!DOCTYPE html>
<html>

<head>
    <title>Event Invitation</title>
</head>

<body>
    <h1>Dear {{ $data['name'] ?? 'User' }}!</h1>
    <p>You are cordially invited to {{ $data['event_name'] ?? 'an event' }}.</p>
    <p><strong>Date:</strong> {{ $data['date'] ?? 'TBD' }}<br>
        <strong>Time:</strong> {{ $data['time'] ?? 'TBD' }}<br>
        <strong>Location:</strong> {{ $data['location'] ?? 'TBD' }}
    </p>
    <p>{{ $data['message'] ?? 'We look forward to your presence.' }}</p>
</body>

</html>