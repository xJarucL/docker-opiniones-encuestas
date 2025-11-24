<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - {{ $tituloEncuesta }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/funciones.js') }}" defer></script>
<script src="{{ asset('js/sweetalert.js') }}" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <style>
        
body {
    font-family: Arial, sans-serif;
    background-color: #faecd6;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    position: relative;
    transition: opacity 0.3s ease;
}

.header {
    position: relative;
    text-align: center;
    margin-bottom: 40px;
    animation: fadeInDown 0.6s ease-out;
}

.header h1 {
    font-size: 2em;
    margin: 0;
    color: #333;
}

.close-button {
    position: absolute;
    top: 10px;
    right: 20px;
    display: inline-block;
}

.close-button img {
    width: 32px;
    height: 32px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.close-button img:hover {
    transform: scale(1.15);
}

.pregunta-block {
    padding: 20px 0;
    position: relative;
    animation: fadeInUp 0.5s ease-out both;
}

.pregunta-block:nth-child(2) { animation-delay: 0.1s; }
.pregunta-block:nth-child(3) { animation-delay: 0.2s; }
.pregunta-block:nth-child(4) { animation-delay: 0.3s; }
.pregunta-block:nth-child(5) { animation-delay: 0.4s; }
.pregunta-block:nth-child(6) { animation-delay: 0.5s; }
.pregunta-block:nth-child(7) { animation-delay: 0.6s; }
.pregunta-block:nth-child(8) { animation-delay: 0.7s; }

.pregunta-titulo {
    font-size: 1.4em;
    margin-bottom: 10px;
    color: #333;
}

.respuesta-row {
    display: flex;
    align-items: center;
    margin: 10px 0;
    transition: all 0.3s ease;
}

.respuesta-row:hover {
    transform: translateX(5px);
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 8px;
    padding: 5px;
    margin-left: -5px;
}

.medal {
    width: 40px;
    text-align: center;
}

.medal img {
    width: 30px;
    height: 30px;
}

.position-number {
    font-weight: bold;
    font-size: 1.1em;
    color: #444;
}

.respuesta-nombre {
    width: 120px;
    font-weight: bold;
}

.bar-container {
    flex-grow: 1;
    background: #f1f1f1;
    border-radius: 10px;
    overflow: hidden;
    margin-left: 10px;
    height: 25px;
}

.progress-bar {
    background: linear-gradient(to right, #00ff00 0%, #00cc00);
    color: white;
    text-align: right;
    padding-right: 10px;
    border-radius: 10px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    transition: width 1s ease-out;
}

.hidden {
    display: none;
}

.toggle-btn {
    display: block;
    margin: 10px auto;
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
}

.toggle-btn:hover {
    background: #0056b3;
}

.pregunta-divider {
    border: none;
    border-top: 4px solid black;
    margin-top: 20px;
}

.repeat-button {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 40px auto;
    background: #28a745;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1em;
    gap: 8px;
    transition: transform 0.6s ease, background 0.3s ease;
}

.repeat-button:hover {
    background: #218838;
}

.repeat-button:active {
    transform: scale(0.95) !important;
}

.repeat-button svg {
    transition: transform 0.6s ease;
}

/* ============ ANIMACIONES ============ */

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
    <div class="container">
        <div class="header">
            <h1>{{ $tituloEncuesta }}</h1>

            <!-- Botón cerrar (X) -->
            <a href="{{ route('inicio') }}" class="close-button">
                <img src="{{ asset('img/close-icon.png') }}" alt="Cerrar">
            </a>
        </div>

        @foreach($resultados as $preguntaId => $respuestas)
            <div class="pregunta-block">
                <h2 class="pregunta-titulo">{{ $respuestas[0]->pregunta }}</h2>

                <div class="respuestas-list">
                    @foreach($respuestas as $index => $r)
                        <div class="respuesta-row {{ $index >= 3 ? 'extra hidden' : '' }}">
                            <div class="medal">
                                @if($index === 0)
                                    <img src="{{ asset('img/trofeo.png') }}" alt="Trofeo">
                                @elseif($index === 1)
                                    <img src="{{ asset('img/medalla-plata.png') }}" alt="Medalla de plata">
                                @elseif($index === 2)
                                    <img src="{{ asset('img/medalla-bronce.png') }}" alt="Medalla de bronce">
                                @else
                                    <span class="position-number">{{ $index + 1 }}</span>
                                @endif
                            </div>

                            {{-- LÍNEA CORREGIDA --}}
                            <div class="respuesta-nombre">{{ $r->opcion ?? 'Sin respuesta' }}</div>

                            <div class="bar-container">
                                <div class="progress-bar" style="width: {{ $r->porcentaje }}%">
                                    {{ $r->porcentaje }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($respuestas) > 3)
                    <button class="toggle-btn" data-target="pregunta-{{ $preguntaId }}">
                        Ver todas las respuestas
                    </button>
                @endif

                <hr class="pregunta-divider">
            </div>
        @endforeach

        @if(isset($primeraPreguntaId) && $primeraPreguntaId)
        <a href="{{ route('presentacion.completa', ['encuestaId' => $encuestaId, 'preguntaId' => $primeraPreguntaId]) }}" 
           class="repeat-button">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 4v6h6M23 20v-6h-6"/>
                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
            </svg>
            Repetir Animación
        </a>
    @endif
    </div>
</body>
</html>
