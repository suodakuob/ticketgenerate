<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket</title>
    <style>
        /* Reset et centrage parfait pour PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f5f5;
        }
        
        /* Conteneur principal adapté au PDF */
        .pdf-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        
        /* Style du ticket */
        .ticket-box {
            background: white;
            border: 5px solid #2ea012;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(46, 160, 18, 0.2);
            position: relative;
        }
        
        /* En-tête */
        .header {
            background: #2ea012;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Détails du ticket */
        .details {
            padding: 25px;
            background: rgba(46, 160, 18, 0.1);
            margin: 20px;
            border-radius: 10px;
            border: 2px dashed #2ea012;
        }
        
        .details p {
            margin: 12px 0;
            font-size: 16px;
            line-height: 1.5;
            display: flex;
            justify-content: space-between;
        }
        
        .details strong {
            color:rgb(6, 17, 4);
            font-weight: bold;
            min-width: 120px;
        }
        
        /* QR Code */
        .qr-code {
            text-align: center;
            padding: 20px;
        }
        
        .qr-code img {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .qr-code p {
            margin-bottom: 15px;
            font-weight: bold;
            color: #2ea012;
        }
        
        /* Pied de page */
        .ticket-footer {
            background: #2ea012;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Formatage du texte */
        .uppercase-first {
            text-transform: lowercase;
            display: inline-block;
        }
        .uppercase-first::first-letter {
            text-transform: uppercase;
        }
        
       
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <div class="ticket-box">
            <div class="header">E-Ticket</div>
            
            <div class="details">
                <p class="uppercase-first"><strong>Match: </strong> {{ $ticket->match->home_team }} vs {{ $ticket->match->away_team }}</p>
                <p class="uppercase-first"><strong>Date & Time: </strong> {{ $ticket->match->match_date->format('F j, Y g:i A') }}</p>
                <p class="uppercase-first"><strong>Stadium: </strong> {{ $ticket->match->stadium }}</p>
                <p class="uppercase-first"><strong>Ticket Number: </strong> {{ $ticket->ticket_number }}</p>
                <p class="uppercase-first"><strong>Status: </strong> Confirmed</p>
            </div>
            
            <div class="qr-code">
                <p>Scan QR Code to Verify</p>
                <img src="{{ $qrBase64 }}" alt="QR Code">
            </div>
            
            <div class="ticket-footer">
                Game On! Let the Magic Begin!
            </div>
        </div>
    </div>
</body>
</html>