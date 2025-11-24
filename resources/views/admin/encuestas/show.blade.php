@extends('admin.admin-layout')

@section('title', 'Resultados - ' . $encuesta->titulo)

@section('content')

<div class="mb-6">
    {{-- ... (Tu código de "Volver" y Título) ... --}}
    <a href="{{ route('admin.encuestas.index') }}" class="text-green-600 hover:text-green-700 mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Volver a Encuestas
    </a>
    <h1 class="text-3xl font-bold text-gray-800 mt-2">Resultados: {{ $encuesta->titulo }}</h1>
    <p class="text-gray-600 mt-1">Análisis de respuestas y estadísticas</p>
</div>

{{-- Alerta flotante global --}}
<div class="mb-6 animate-fade-slide relative z-50" style="animation-delay: 0.1s;">
    <x-msj-alert />
</div>

<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    {{-- ... (Tu grid de "Descripción", "Categoría", "Estado") ... --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-500 mb-1">Descripción</h3>
            <p class="text-gray-800">{{ $encuesta->descripcion ?: 'Sin descripción' }}</p>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-500 mb-1">Categoría</h3>
            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">
                {{ $encuesta->categoria->nombre ?? 'Sin categoría' }}
            </span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-500 mb-1">Estado</h3>
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $encuesta->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $encuesta->estado ? 'Activa' : 'Inactiva' }}
            </span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-500 mb-1">Creada</h3>
            <p class="text-gray-800">{{ $encuesta->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    
    {{-- TARJETA 1: USUARIOS QUE YA RESPONDIERON (PARTICIPACIÓN) --}}
    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="flex justify-between items-start z-10 relative">
            <div>
                <p class="text-sm text-blue-600 font-bold uppercase tracking-wider mb-1">Participación</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-3xl font-extrabold text-blue-800">{{ $usuariosParticipantes }}</p>
                    <span class="text-sm text-blue-600 font-medium">de {{ $totalUsuarios }} usuarios</span>
                </div>
            </div>
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Barra de Progreso Visual --}}
        
    </div>

    {{-- TARJETA 2: USUARIOS PENDIENTES --}}
    <div class="bg-orange-50 p-6 rounded-xl border border-orange-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-orange-600 font-bold uppercase tracking-wider mb-1">Faltan por Votar</p>
                <p class="text-3xl font-extrabold text-orange-800">{{ $usuariosPendientes }}</p>
                <p class="text-xs text-orange-600 mt-1 font-medium">Usuarios pendientes</p>
            </div>
            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- TARJETA 3: ESTADO PÚBLICO/PRIVADO (Ocupa 2 columnas) --}}
    <div class="md:col-span-2"> 
        @if($encuesta->resultados_publicos)
            <div class="bg-green-50 p-6 rounded-xl border border-green-300 h-full flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-1.5 bg-green-200 rounded-full text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-green-800">Resultados Públicos</h3>
                </div>
                <p class="text-sm text-green-700 font-medium ml-1">
                    ¡Liberados! Todos los usuarios pueden ver las gráficas.
                </p>
            </div>
        @else
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-300 h-full flex flex-col justify-between hover:bg-white transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            Resultados Privados
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Solo el administrador puede ver esto.</p>
                    </div>
                </div>
                
                <form action="{{ route('admin.encuestas.liberar', $encuesta) }}" method="POST" class="mt-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full group bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                        <span>Liberar Resultados</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@foreach($encuesta->preguntas as $pregunta)
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $pregunta->texto }}</h3>
    <p class="text-sm text-gray-500 mb-4">Tipo: 
        {{-- ... (Tu lógica de tipo de pregunta) ... --}}
    </p>

    @if($pregunta->tipo == 'multiple' || $pregunta->tipo == 'rating')
        <div class="space-y-3">
            @php
                // --- LÓGICA DE CÁLCULO MEJORADA ---
                // Filtra respuestas nulas o vacías ANTES de agrupar
                $respuestasValidas = $pregunta->respuestas->whereNotNull('respuesta')->filter(function ($value) {
                    return $value->respuesta != '';
                });
                $respuestasAgrupadas = $respuestasValidas->groupBy('respuesta')->map->count();
                $total = $respuestasAgrupadas->sum();
            @endphp
            
            @if($total > 0)
                {{-- Ordena de mayor a menor --}}
                @foreach($respuestasAgrupadas->sortDesc() as $opcion => $cantidad) 
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $opcion }}</span>
                        <span class="text-sm font-semibold text-gray-800">
                            {{ $cantidad }} ({{ number_format(($cantidad / $total) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-full rounded-full transition-all duration-500"
                             style="width: {{ ($cantidad / $total) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-500 text-center py-4">Sin respuestas aún</p>
            @endif
        </div>
    @else
        <div class="space-y-3 max-h-96 overflow-y-auto">
            {{-- Filtra respuestas vacías/nulas también en texto libre --}}
            @forelse($pregunta->respuestas->whereNotNull('respuesta')->filter(function ($value) { return $value->respuesta != ''; }) as $respuesta)
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-800">{{ $respuesta->respuesta }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $respuesta->created_at->diffForHumans() }}
                </p>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Sin respuestas aún</p>
            @endforelse
        </div>
    @endif
</div>
@endforeach

{{-- ================================================ --}}
{{-- ============ BOTÓN FLOTANTE CORREGIDO ============ --}}
{{-- ================================================ --}}
@if($encuesta->resultados_publicos)
    {{-- Si están liberados, muestra el enlace funcional --}}
    <a href="{{ route('podiotwo', ['encuestaId' => $encuesta->id, 'preguntaIndex' => 0]) }}"
        target="_blank" {{-- Abre en pestaña nueva --}}
        class="boton-flotante"
        title="Ver la presentación pública de resultados">
        Ver Resultados (Público)
    </a>
@else
    {{-- Si NO están liberados, muestra un botón desactivado --}}
    <button type="button"
       class="boton-flotante"
       style="background-color: #71717a; cursor: not-allowed; opacity: 0.8;"
       title="Debes liberar los resultados primero"
       disabled>
        Resultados Privados
    </button>
@endif

<style>
{{-- ... (Tu CSS del botón flotante no cambia) ... --}}
</style>

@endsection

@section('styles')
{{-- ... (Tu sección @styles no cambia) ... --}}
@endsection

@section('scripts')
{{-- ... (Tu sección @scripts no cambia) ... --}}
@endsection