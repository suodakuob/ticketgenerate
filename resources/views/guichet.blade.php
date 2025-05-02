<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Guichet Virtuel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* 🎥 Vidéo plein écran */
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
        }

        /* 🔲 Overlay centré */
        .overlay {
            position: relative;
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.3);
            animation: fadeIn 1.5s ease;
        }

        /* 🎨 Boîte effet verre flouté */
        .box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        .box h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        /* 🔘 Boutons modernes */
        .btn-guichet {
    background: linear-gradient(145deg, #28d1a6, #198754);
    border: none;
    padding: 16px 24px;
    margin: 12px auto;
    width: 80%;
    max-width: 300px;
    color: white;
    font-size: 18px;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    text-decoration: none;
    display: block;
    text-align: center;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}


.btn-guichet:hover {
    transform: translateY(-2px) scale(1.03);
    background: linear-gradient(145deg, #198754, #28d1a6);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
}


        /* ✔️ Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 📱 Responsive */
        @media (max-width: 500px) {
            .box h1 {
                font-size: 24px;
            }
            .btn-guichet {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- 🎥 Vidéo de fond -->
    <video class="video-bg" autoplay muted loop playsinline>
        <source src="{{ asset('videos/allianz.mp4') }}" type="video/mp4">
        Votre navigateur ne supporte pas les vidéos HTML5.
    </video>

    <!-- 📦 Contenu principal -->
    <div class="overlay">
        <div class="box">
            <h1>Bienvenue au Guichet Virtuel</h1>

            @auth
    <a href="{{ route('matches.index') }}" class="btn-guichet">
        🎯 Réserver un Match
    </a>
@else
    <a href="{{ route('login') }}" class="btn-guichet">
        🎯 Connectez-vous pour réserver un Match
    </a>
@endauth


            @auth
                <a href="{{ route('my-tickets') }}" class="btn-guichet">
                    🎫 Voir votre Ticket
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-guichet">
                    🔐 Se connecter pour voir vos Tickets
                </a>
            @endauth
        </div>
    </div>

</body>
</html>
