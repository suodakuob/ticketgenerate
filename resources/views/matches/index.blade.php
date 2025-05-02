<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matches - Ticket360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto">

            <!-- 🔝 Top navigation buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <a href="{{ route('home') }}" 
                   class="inline-block px-5 py-2 bg-gray-800 text-white rounded-md font-semibold shadow hover:bg-gray-900 transition">
                    🏠 Revenir à l’accueil
                </a>
                <a href="{{ route('my-tickets') }}" 
                   class="inline-block px-5 py-2 bg-green-600 text-white rounded-md font-semibold shadow hover:bg-green-700 transition">
                    🎫 Voir mes Tickets
                </a>
            </div>

            <h1 class="text-3xl font-bold mb-6">Upcoming Matches</h1>

            @if($matches->isEmpty())
                <p class="text-gray-600">No upcoming matches available.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($matches as $match)
                        <x-match-card :match="$match" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $matches->links() }}
                </div>
            @endif

        </div>
    </div>

</body>
</html>
