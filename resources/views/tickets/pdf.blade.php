<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { width: 100%; padding: 20px; }
        .ticket-box { border: 2px solid black; padding: 20px; width: 50%; margin: auto; text-align: left; }
        .header { font-size: 24px; font-weight: bold; text-align: center; }
        .details { margin-top: 20px; }
        .details p { margin: 5px 0; font-size: 16px; }
        .qr-code { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-box">
            <div class="header">E-Ticket</div>
            <div class="details">
                <p><strong>Match:</strong> {{ $ticket->match->home_team }} vs {{ $ticket->match->away_team }}</p>
                <p><strong>Date & Time:</strong> {{ $ticket->match->match_date->format('F j, Y g:i A') }}</p>
                <p><strong>Stadium:</strong> {{ $ticket->match->stadium }}</p>
                <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Status:</strong> Confirmed</p>
            </div>

            <div class="qr-code">
                <p><strong>Scan QR Code to Verify</strong></p>
                <img src="{{ storage_path('app/public/' . $ticket->qr_code) }}" width="150">
            </div>
        </div>
    </div>
</body>
</html>
