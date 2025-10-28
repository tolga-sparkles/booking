<!DOCTYPE html>
<html>
<head>
    <title>Reservation Cancelled</title>
</head>
<body>
    <h1>Reservation Cancelled</h1>
    <p>Hello {{ $reservation->user->name }},</p>
    <p>Your reservation scheduled for {{ $reservation->start_time }} with {{ $reservation->expert->name ?? 'N/A' }} has been cancelled.</p>
    <p>If you did not request this cancellation, please contact us immediately.</p>
    <p>Thank you.</p>
</body>
</html>
