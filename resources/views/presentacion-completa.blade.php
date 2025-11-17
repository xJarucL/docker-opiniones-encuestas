<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentación - {{ $tituloEncuesta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            width: 100%;
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #e8dcc4 0%, #f5f0e8 100%);
            overflow: hidden;
        }

        /* ============ SECCIÓN 1: SOBRE ============ */
        #section1 {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease;
            position: relative;
            z-index: 10;
        }

        #section1.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .envelope-wrapper {
            height: 280px;
            width: 420px;
            position: relative;
            opacity: 0;
            animation: fadeIn 1s ease-in forwards;
            cursor: pointer;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .envelope {
            height: 280px;
            width: 420px;
            background-color: #E8D5B7;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .letter {
            position: absolute;
            top: 0;
            width: 100%;
            height: 85%;
            background-color: #FFFEF7;
            border-radius: 8px;
            z-index: 2;
            transition: transform 0.8s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .envelope-base {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            border-top: 140px solid transparent;
            border-right: 210px solid #D4BA96;
            border-bottom: 140px solid #D4BA96;
            border-left: 210px solid #D4BA96;
            z-index: 3;
        }

        .lid {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            border-right: 210px solid transparent;
            border-bottom: 140px solid transparent;
            border-left: 210px solid transparent;
            transform-origin: top;
            transition: transform 0.6s ease;
        }

        .lid.one {
            border-top: 140px solid #C4A878;
            transform: rotateX(0deg);
            z-index: 4;
        }

        .lid.two {
            border-top: 140px solid #B89968;
            transform: rotateX(90deg);
            z-index: 1;
        }

        .seal-ribbon {
            position: absolute;
            width: 90px;
            height: auto;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
        }

        .envelope-wrapper.opening .lid.one {
            transform: rotateX(90deg);
        }

        .envelope-wrapper.opening .lid.two {
            transform: rotateX(180deg);
            transition-delay: 0.2s;
        }

        .envelope-wrapper.opening .letter {
            transform: translateY(-80px);
            transition-delay: 0.4s;
        }

        .envelope-wrapper.opening .seal-ribbon {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .click-hint {
            position: absolute;
            bottom: 30px;
            color: #8B7355;
            font-size: 14px;
            opacity: 0;
            animation: fadeIn 1s ease-in forwards;
            animation-delay: 1s;
        }

        /* ============ SECCIÓN 2: PREGUNTA ============ */
        #section2 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
            z-index: 5;
        }

        #section2.active {
            opacity: 1;
            pointer-events: all;
        }

        .box {
            background: white;
            border-radius: 15px;
            padding: 60px 100px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            text-align: center;
            transform: scale(0.5) translateY(50px);
            transition: all 1s ease-out;
        }

        #section2.active .box {
            transform: scale(1) translateY(0);
        }

        .box h1 {
            font-size: 2.8rem;
            color: #2c2c2c;
            font-weight: 500;
            letter-spacing: 2px;
        }

        /* ============ SECCIÓN 3: PODIO ============ */
        #section3 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            padding-bottom: 50px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
            z-index: 1;
        }

        #section3.active {
            opacity: 1;
            pointer-events: all;
        }

        .spotlight {
            position: absolute;
            top: 0;
            width: 500px;
            height: 900px;
            opacity: 0.50;
            pointer-events: none;
        }

        .spotlight.left {
            left: 0;
            background: linear-gradient(135deg, 
                rgb(255, 255, 255) 0%, 
                rgba(255, 255, 255, 0.3) 40%, 
                rgba(255, 255, 255, 0) 100%);
            animation: spotlightRotateLeft 1s ease-in-out infinite;
            clip-path: polygon(0 0, 30% 0, 100% 100%, 0 100%);
        }

        .spotlight.right {
            right: 0;
            background: linear-gradient(225deg, 
                rgb(255, 255, 255) 0%, 
                rgba(255, 255, 255, 0.3) 40%, 
                rgba(255, 255, 255, 0) 100%);
            animation: spotlightRotateRight 1s ease-in-out infinite;
            clip-path: polygon(70% 0, 100% 0, 100% 100%, 0 100%);
        }

        @keyframes spotlightRotateLeft {
            0% { transform: rotate(-20deg); }
            50% { transform: rotate(35deg); }
            100% { transform: rotate(-20deg); }
        }

        @keyframes spotlightRotateRight {
            0% { transform: rotate(20deg); }
            50% { transform: rotate(-35deg); }
            100% { transform: rotate(20deg); }
        }

        .podium-base {
            position: absolute;
            bottom: 50px;
            width: 800px;
            height: 60px;
            background: linear-gradient(to bottom, #c97d7d 0%, #a85454 100%);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transform: translateX(-150%);
        }

        #section3.active .podium-base {
            animation: slideInBase 1s ease-out forwards;
        }

        @keyframes slideInBase {
            to { transform: translateX(0); }
        }

        .podium-pillar {
            position: absolute;
            bottom: 110px;
            width: 200px;
            background: linear-gradient(to bottom, #ffd700 0%, #f4c542 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(500px);
            opacity: 0;
        }

        .pillar-number {
            font-size: 120px;
            font-weight: bold;
            color: white;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3);
            margin-top: 40px;
        }

        .pillar-name {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            position: absolute;
            top: -50px;
        }

        .pillar-percent {
            position: absolute;
            bottom: 10px;
            font-size: 26px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4);
        }

        .pillar-3 {
            left: calc(50% - 330px);
            height: 200px;
        }

        .pillar-1 {
            left: calc(50% - 100px);
            height: 320px;
        }

        .pillar-2 {
            left: calc(50% + 130px);
            height: 260px;
        }

        #section3.active .pillar-3 {
            animation: riseUpPillar 0.8s ease-out forwards;
            animation-delay: 1s;
        }

        #section3.active .pillar-1 {
            animation: riseUpPillar 0.8s ease-out forwards;
            animation-delay: 2.2s;
        }

        #section3.active .pillar-2 {
            animation: riseUpPillar 0.8s ease-out forwards;
            animation-delay: 1.6s;
        }

        @keyframes riseUpPillar {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .star {
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 80px;
            opacity: 0;
        }

        #section3.active .star {
            animation: starAppear 1.2s ease-out forwards;
            animation-delay: 3.2s;
        }

        @keyframes starAppear {
            0% {
                transform: translateX(-50%) translateY(100px) scale(0) rotate(0deg);
                opacity: 0;
            }
            60% {
                transform: translateX(-50%) translateY(-20px) scale(1.2) rotate(720deg);
                opacity: 1;
            }
            100% {
                transform: translateX(-50%) translateY(0) scale(1) rotate(720deg);
                opacity: 1;
            }
        }

        /* Botón ver resultados */
        .ver-resultados-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(to right, #a85454, #c97d7d);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .ver-resultados-btn.show {
            opacity: 1;
            pointer-events: all;
        }

        .ver-resultados-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>
    <!-- SECCIÓN 1: SOBRE -->
    <div id="section1">
        <div class="envelope-wrapper" id="envelopeWrapper">
            <img src="{{ asset('img/Sello1.png') }}" alt="Sello" class="seal-ribbon">
            <div class="lid one"></div>
            <div class="lid two"></div>
            <div class="envelope-base"></div>
            <div class="letter"></div>
        </div>
        <div class="click-hint">Haz clic en el sobre</div>
    </div>

    <!-- SECCIÓN 2: PREGUNTA -->
    <div id="section2">
        <div class="box">
            <h1>Los nominados a<br>{{ $tituloEncuesta }}<br>son .....</h1>
        </div>
    </div>
<!-- SECCIÓN 3: PODIO -->
<div id="section3">
    <div class="spotlight left"></div>
    <div class="spotlight right"></div>
    
    <div class="podium-base"></div>
    
    @if(isset($resultados) && count($resultados) > 0)
        <!-- Pilar 3ro (si existe) -->
        @if(isset($resultados[2]))
            <div class="podium-pillar pillar-3">
                <div class="pillar-name">{{ $resultados[2]->opcion }}</div>
                <div class="pillar-number">3</div>
                <div class="pillar-percent">{{ $resultados[2]->porcentaje }}%</div>
            </div>
        @endif
        
        <!-- Pilar 1ro -->
        @if(isset($resultados[0]))
            <div class="podium-pillar pillar-1">
                <div class="pillar-name">{{ $resultados[0]->opcion }}</div>
                <div class="pillar-number">1</div>
                <div class="pillar-percent">{{ $resultados[0]->porcentaje }}%</div>
                <div class="star">⭐</div>
            </div>
        @endif
        
        <!-- Pilar 2do (si existe) -->
        @if(isset($resultados[1]))
            <div class="podium-pillar pillar-2">
                <div class="pillar-name">{{ $resultados[1]->opcion }}</div>
                <div class="pillar-number">2</div>
                <div class="pillar-percent">{{ $resultados[1]->porcentaje }}%</div>
            </div>
        @endif
    @else
        <!-- Mensaje si no hay datos -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #333;">
            <h2>No hay resultados disponibles</h2>
            <p>Aún no hay votos registrados para esta pregunta</p>
        </div>
    @endif
</div>


    <!-- Botón ver resultados -->
    @if($siguientePregunta)
        {{-- Si hay una siguiente pregunta, enlaza a la animación de esa pregunta --}}
        <a href="{{ route('presentacion.completa', ['encuestaId' => $siguientePregunta->encuesta_id, 'preguntaId' => $siguientePregunta->id]) }}" 
           id="verResultadosBtn" 
           class="ver-resultados-btn">
           Siguiente Pregunta
        </a>
    @else
        {{-- Si es la última, enlaza a la vista de resultados finales (la de 'PresentaciontwoController') --}}
        <a href="{{ route('resultadostwo', ['encuestaId' => $encuestaId]) }}" 
           id="verResultadosBtn" 
           class="ver-resultados-btn">
           Ver Resumen Final
        </a>
    @endif

    <script>
        const envelopeWrapper = document.getElementById('envelopeWrapper');
        const section1 = document.getElementById('section1');
        const section2 = document.getElementById('section2');
        const section3 = document.getElementById('section3');
        const verResultadosBtn = document.getElementById('verResultadosBtn');

        envelopeWrapper.addEventListener('click', () => {
            envelopeWrapper.classList.add('opening');
            
            setTimeout(() => {
                section1.classList.add('hidden');
                section2.classList.add('active');
                
                setTimeout(() => {
                    section2.classList.remove('active');
                    section3.classList.add('active');
                    
                    // Mostrar botón después del podio
                    setTimeout(() => {
                        verResultadosBtn.classList.add('show');
                    }, 4000);
                }, 3000);
            }, 2000);
        });
    </script>
</body>
</html>