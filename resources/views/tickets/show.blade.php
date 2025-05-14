<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Your E-Ticket - Ticket360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
             font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
             line-height: 1.5;
             background-color: #f3f4f6; /* bg-gray-100 */
             color: #1f2937; /* gray-800 */
         }

        /* Styles pour la partie "ticket visuel" sur la page HTML */
        .ticket-display-box {
            max-width: 500px; /* Limiter la largeur comme dans le PDF */
            margin: 40px auto; /* Centrer et ajouter des marges */
            background: white;
            border: 5px solid #2ea012; /* Bordure verte */
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(46, 160, 18, 0.2);
        }

         .ticket-display-box .header {
            background: #2ea012; /* Vert plus foncé */
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-display-box .details {
            padding: 25px;
            background: rgba(46, 160, 18, 0.1); /* Fond vert pâle */
            margin: 20px;
            border-radius: 10px;
            border: 2px dashed #2ea012; /* Bordure tirets verte */
        }

         .ticket-display-box .details p {
            margin: 12px 0;
            font-size: 16px;
            line-height: 1.5;
            display: flex;
            justify-content: space-between;
             align-items: flex-start;
             flex-wrap: wrap;
        }

         .ticket-display-box .details p strong {
            color:rgb(6, 17, 4); /* Vert très foncé */
            font-weight: bold;
            min-width: 140px; /* Ajusté pour plus d'espace pour les étiquettes */
            margin-right: 10px;
            flex-shrink: 0;
        }
         .ticket-display-box .details p span {
             flex-grow: 1;
             text-align: right; /* Aligner les valeurs à droite */
         }

        /* Styles pour le badge de statut dans la partie ticket visuel */
         .ticket-display-box .details p span.status-badge {
              text-align: left; /* Le badge ne s'aligne pas à droite */
              display: inline-block;
              padding: 4px 8px;
              border-radius: 10px;
              font-size: 14px;
              font-weight: normal;
              min-width: auto;
              /* Couleurs des badges comme dans le PDF */
              background-color: #d1fae5; /* green-100 */
              color: #065f46; /* green-800 */
         }
         .ticket-display-box .details p span.status-badge.pending {
             background-color: #fffbe6; /* yellow-100 */
             color: #78350f; /* yellow-800 */
         }
          .ticket-display-box .details p span.status-badge.qr_error {
             background-color: #fee2e2; /* red-100 */
             color: #991b1b; /* red-800 */
         }


        .ticket-display-box .qr-code {
            text-align: center;
            padding: 20px;
            border-top: 2px dashed rgba(46, 160, 18, 0.3); /* Ajout d'une ligne pour séparer les détails du QR */
        }

        .ticket-display-box .qr-code img {
            width: 180px; /* Taille un peu plus grande pour l'affichage web */
            height: 180px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: block;
            margin: 0 auto 15px auto; /* Centrer l'image */
        }

        .ticket-display-box .qr-code p {
            margin: 0; /* Retirer la marge par défaut */
            font-weight: bold;
            color: #2ea012;
            font-size: 16px;
        }


        .ticket-display-box .ticket-footer {
            background: #2ea012; /* Vert plus foncé */
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        /* ========================================= */
        /* Styles spécifiques pour l'impression (ajoutés) */
        /* ========================================= */
        @media print {
            body {
                background-color: #fff !important; /* Fond blanc forcé pour l'impression */
                margin: 0;
                padding: 0;
            }

            /* Masquer les éléments non essentiels à l'impression */
            .mb-6.flex, /* Boutons de navigation */
            h2.text-3xl, /* Titre de la page */
            .bg-green-100, /* Messages de succès */
            .bg-red-100, /* Messages d'erreur */
            .download-button-container, /* Le bouton d'impression lui-même */
             .bg-white.shadow-md.rounded-lg.p-6.mb-8 /* Le bloc "Détails du Ticket" si vous ne voulez que le ticket visuel */
            {
                display: none !important;
            }

            /* Ajuster le ticket visuel pour l'impression */
            .ticket-display-box {
                margin: 0 auto; /* Centrer sans marges supérieures/inférieures fortes */
                box-shadow: none; /* Retirer l'ombre */
                border: 1px solid #000; /* Bordure plus simple si besoin */
                max-width: 100%; /* Permettre d'utiliser plus de largeur de page si nécessaire */
                /* Ajustez la largeur si vous voulez un format spécifique, par exemple 150mm */
                 width: 150mm; /* Exemple: une largeur fixe pour l'impression */
            }

            /* Optionnel: Ajuster la taille du QR code pour l'impression */
            .ticket-display-box .qr-code img {
                 width: 120px; /* Plus petit pour un PDF/impression */
                 height: 120px;
                 /* Autres ajustements si besoin */
             }

             /* Optionnel: Ajuster les tailles de police pour l'impression si besoin */
             .ticket-display-box .header {
                 font-size: 20px;
                 padding: 10px;
             }
             .ticket-display-box .details p {
                  font-size: 14px;
                  margin: 8px 0;
             }
             .ticket-display-box .details {
                 margin: 10px;
                 padding: 15px;
             }
             .ticket-display-box .ticket-footer {
                  font-size: 14px;
                  padding: 10px;
             }

            /* Forcer l'affichage de l'arrière-plan pour l'en-tête et le pied de page */
            .ticket-display-box .header,
            .ticket-display-box .ticket-footer {
                 -webkit-print-color-adjust: exact; /* Pour Chrome, Safari */
                 color-adjust: exact; /* Standard */
            }
        }

    </style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen antialiased">

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto">

            <!-- Back buttons (Ces boutons seront masqués à l'impression par le CSS @media print) -->
            <div class="mb-6 flex flex-wrap items-center gap-4">
                <a href="{{ route('my-tickets') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-sm print-hidden">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Retourner à Mes Tickets
                </a>
                 <a href="{{ route('matches.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm print-hidden">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Voir d'autres matchs
                </a>
            </div>

            <!-- Titre de la page (Sera masqué à l'impression) -->
            <h2 class="text-3xl font-bold text-center mb-6 print-hidden">Votre E-Ticket</h2>

            {{-- Display messages (Seront masqués à l'impression) --}}
             @if (session('success'))
                 <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 print-hidden" role="alert">
                     <p class="font-bold">Succès!</p>
                     <p>{{ session('success') }}</p>
                 </div>
             @endif
              @if (session('error'))
                 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 print-hidden" role="alert">
                     <p class="font-bold">Erreur!</p>
                     <p>{{ session('error') }}</p>
                 </div>
             @endif

             {{-- Basic Ticket Details Summary (Sera masqué à l'impression si vous le souhaitez - voir CSS) --}}
             {{-- Si vous VOULEZ que ce bloc s'imprime, retirez la classe .print-hidden et l'entrée dans le CSS @media print --}}
             <div class="bg-white shadow-md rounded-lg p-6 mb-8 print-hidden">
                 <h3 class="text-xl font-semibold mb-4">Détails Complémentaires</h3> {{-- Changed title --}}
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                         <p class="text-gray-600">Match:</p>
                         <p class="font-medium">{{ $ticket->match->home_team ?? 'N/A' }} vs {{ $ticket->match->away_team ?? 'N/A' }}</p>
                     </div>
                     <div>
                         <p class="text-gray-600">Date & Heure:</p>
                         <p class="font-medium">{{ $ticket->match->match_date->format('d/m/Y H:i') ?? 'N/A' }}</p>
                     </div>
                     <div>
                         <p class="text-gray-600">Stade:</p>
                         <p class="font-medium">{{ $ticket->match->stadium ?? 'N/A' }}</p>
                     </div>
                     @if($ticket->section)
                         <div>
                             <p class="text-gray-600">Section:</p>
                             <p class="font-medium">{{ $ticket->section->name ?? 'N/A' }} ({{ $ticket->section->section_id ?? 'N/A' }})</p>
                         </div>
                     @endif
                     @if($ticket->seat_number)
                         <div>
                             <p class="text-gray-600">Siège:</p>
                             <p class="font-medium">{{ $ticket->seat_number }}</p>
                         </div>
                     @endif
                      <div>
                         <p class="text-gray-600">Numéro de Ticket:</p>
                         <p class="font-medium">{{ $ticket->ticket_number ?? 'N/A' }}</p>
                     </div>
                      @if($ticket->price)
                         <div>
                            <p class="text-gray-600">Prix:</p>
                            <p class="font-medium">Mad {{ number_format($ticket->price, 2) }}</p>
                         </div>
                      @endif
                     <div>
                         <p class="text-gray-600">Statut:</p>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 {{ $ticket->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($ticket->status === 'qr_error' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                 {{ ucfirst($ticket->status) }}
                         </span>
                     </div>
                 </div>
             </div>


             {{-- Display the ticket content directly here --}}
             {{-- C'est ce bloc qui sera imprimé --}}
             <div class="ticket-display-box">
                 <div class="header">E-Ticket</div>

                 <div class="details">
                     <p><strong>Match:</strong> <span>{{ $ticket->match->home_team ?? 'N/A' }} vs {{ $ticket->match->away_team ?? 'N/A' }}</span></p>
                     <p><strong>Date & Heure:</strong> <span>{{ $ticket->match->match_date->format('d/m/Y H:i') ?? 'N/A' }}</span></p>
                     <p><strong>Stade:</strong> <span>{{ $ticket->match->stadium ?? 'N/A' }}</span></p>
                     @if($ticket->section)
                          <p><strong>Section:</strong> <span>{{ $ticket->section->name ?? 'N/A' }} ({{ $ticket->section->section_id ?? 'N/A' }})</p>
                     @endif
                      @if($ticket->seat_number)
                         <p><strong>Siège:</strong> <span>{{ $ticket->seat_number }}</span></p>
                      @endif
                     <p><strong>Numéro de Ticket:</strong> <span>{{ $ticket->ticket_number ?? 'N/A' }}</span></p>
                      @if($ticket->price)
                         <p><strong>Prix:</strong> <span>Mad {{ number_format($ticket->price, 2) }}</span></p>
                      @endif
                     <p>
                         <strong>Statut:</strong>
                         <span class="status-badge {{ $ticket->status }}">
                             {{ ucfirst($ticket->status) }}
                         </span>
                     </p>
                 </div>

                 <div class="qr-code">
                     {{-- Utilise la variable $qrBase64 passée par le contrôleur --}}
                     @if($qrBase64)
                         <p>Scan QR Code to Verify</p>
                         <img src="{{ $qrBase64 }}" alt="QR Code">
                     @else
                         <p class="text-red-600">QR Code non disponible.</p>
                     @endif
                 </div>

                 <div class="ticket-footer">
                     Game On! Let the Magic Begin!
                 </div>
             </div>

            {{-- Bouton pour imprimer le ticket --}}
             <div class="mt-8 text-center download-button-container"> {{-- Réutilisation de la classe pour le centrage et marge --}}
                 <button onclick="window.print()"
                         class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md font-semibold shadow hover:bg-indigo-700 transition">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                     Imprimer le E-Ticket
                 </button>
             </div>


        </div>
    </div>

</body>
</html>