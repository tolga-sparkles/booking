<!DOCTYPE html>
<html>
<head>
    <title>Reservation Confirmation</title>
</head>
<body>
    <h1>Reservation Confirmation</h1>
    <p>Hello {{ $reservation->user->name }},</p>
    <p>Your reservation has been confirmed. Here are the details:</p>
    <ul>
        <li><strong>Expert:</strong> {{ $reservation->expert->name ?? 'N/A' }}</li>
        <li><strong>Start Time:</strong> {{ $reservation->start_time }}</li>
        <li><strong>End Time:</strong> {{ $reservation->end_time }}</li>
    </ul>
    <p>Thank you for using our service!</p>
</body>
</html>
