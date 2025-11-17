<!DOCTYPE html>
<html lang="es">
@auth
<x-menu />
@endauth
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes floatImg {
            0% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); }
        }

        @keyframes floatStar {
            from { transform: translateY(0); opacity: 0.3; }
            to { transform: translateY(200px); opacity: 0; }
        }

        .animate-gradient {
            animation: gradient 12s ease infinite;
            background-size: 300% 300%;
        }

        .animate-img {
            animation: floatImg 3s ease-in-out infinite;
        }

        .animate-img-delay {
            animation: floatImg 3s ease-in-out 0.6s infinite;
        }

        .star {
            animation: floatStar linear infinite;
        }
    </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-hidden">

    <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-purple-700 to-purple-500 animate-gradient"></div>

    <div class="absolute inset-0 pointer-events-none">
        <div class="w-1 h-1 bg-white rounded-full opacity-30 absolute top-10 left-20 star" style="animation-duration: 8s"></div>
        <div class="w-1 h-1 bg-white rounded-full opacity-30 absolute top-48 left-72 star" style="animation-duration: 12s"></div>
        <div class="w-1 h-1 bg-white rounded-full opacity-30 absolute top-80 left-1/3 star" style="animation-duration: 10s"></div>
        <div class="w-1 h-1 bg-white rounded-full opacity-30 absolute top-1/4 left-1/2 star" style="animation-duration: 14s"></div>
        <div class="w-1 h-1 bg-white rounded-full opacity-30 absolute top-2/3 left-3/4 star" style="animation-duration: 9s"></div>
    </div>

    <div class="relative z-10 max-w-xl text-center text-white px-6">

        <h1 class="text-5xl font-bold drop-shadow-[0_0_15px_rgba(255,255,255,0.6)] mb-3">
            ¡Vaya!
        </h1>

        <p class="text-lg mb-6 leading-relaxed">
            Parece que hemos perdido tu página.
            No te preocupes, puedes
            <a href="{{ route('inicio') }}" class="font-bold underline hover:text-purple-200 transition">
                volver a la página principal.
            </a>
        </p>


    </div>

</body>
</html>
