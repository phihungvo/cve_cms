<!DOCTYPE html>
<html>

<head>
    <title>Notification Email</title>
</head>

<body>
    <h1>Hello, {{ $data['name'] ?? 'User' }}!</h1>
    <p>{{ $data['message'] ?? 'This is a notification email.' }}</p>
</body>

</html>