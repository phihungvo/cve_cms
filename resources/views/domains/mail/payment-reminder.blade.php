<!DOCTYPE html>
<html>

<head>
    <title>Payment Reminder</title>
</head>

<body>
    <h1>Dear {{ $data['name'] ?? 'User' }}!</h1>
    <p>This is a reminder for your pending payment:</p>
    <p><strong>Invoice Number:</strong> {{ $data['invoice_number'] ?? 'N/A' }}<br>
        <strong>Amount:</strong> {{ $data['amount'] ?? 'N/A' }}<br>
        <strong>Due Date:</strong> {{ $data['due_date'] ?? 'TBD' }}
    </p>
    <p>{{ $data['message'] ?? 'Please settle the payment at your earliest convenience.' }}</p>
    <p><a href="{{ $data['payment_link'] ?? '#' }}">Pay Now</a></p>
</body>

</html>