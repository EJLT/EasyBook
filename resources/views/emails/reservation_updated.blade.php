<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Updated</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f9; padding: 20px;">
<div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
    <h2 style="color: #2196F3; text-align: center;">Reservation Updated</h2>
    <p>Hi {{ $user->name }},</p>
    <p>Your reservation has been updated with the following details:</p>
    <ul>
        <li><strong>Business:</strong> {{ $business->name }}</li>
        <li><strong>Date:</strong> {{ $reservation->date }}</li>
        <li><strong>Time:</strong> {{ $reservation->time }}</li>
        <li><strong>Status:</strong> {{ $reservation->status }}</li>
    </ul>
    <p>If you have any questions, feel free to contact us.</p>
    <footer style="margin-top: 20px; text-align: center; font-size: 0.9em; color: #888;">
        &copy; {{ now()->year }} EasyBook. All rights reserved.
    </footer>
</div>
</body>
</html>
