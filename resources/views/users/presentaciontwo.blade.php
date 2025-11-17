<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pregunta</title>
    <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/funciones.js') }}" defer></script>
<script src="{{ asset('js/sweetalert.js') }}" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        const encuestaId = {{ $encuestaId }};
        const preguntaIndex = {{ $preguntaIndex }};
    </script>
</head>
<body>
    <div class="container">
        <div class="box animate">
            <h1>{{ $tituloEncuesta }}</h1>
        </div>
    </div>
</body>
</html>
