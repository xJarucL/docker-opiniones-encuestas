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
    <div class="bg-blue-50 p-6 rounded-xl">
        {{-- ... (Tu tarjeta "Total Respuestas") ... --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-blue-600 font-semibold mb-1">Total Respuestas</p>
                <p class="text-3xl font-bold text-blue-700">{{ $totalRespuestas }}</p>
            </div>
            <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
    </div>
    <div class="bg-green-50 p-6 rounded-xl">
        {{-- ... (Tu tarjeta "Preguntas") ... --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-green-600 font-semibold mb-1">Preguntas</p>
                <p class="text-3xl font-bold text-green-700">{{ $encuesta->preguntas->count() }}</p>
            </div>
            <svg class="w-10 h-10 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    {{-- ================================================ --}}
    {{-- ========= BLOQUE AÑADIDO PARA LIBERAR ========= --}}
    {{-- ================================================ --}}
    <div class="md:col-span-2"> {{-- Ocupa 2 columnas --}}
        @if($encuesta->resultados_publicos)
            <div class="bg-green-50 p-6 rounded-xl border border-green-300 h-full flex flex-col justify-center">
                <h3 class="text-lg font-bold text-green-800 mb-2">Resultados Públicos</h3>
                <p class="text-sm text-green-700">
                    ¡Liberados! El público ya puede acceder a la presentación de resultados.
                </p>
                {{-- Opcional: Puedes agregar un botón de "Ocultar" aquí si lo deseas --}}
            </div>
        @else
            <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-300 h-full flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-yellow-800 mb-2">Resultados Privados</h3>
                    <p class="text-sm text-yellow-700 mb-4">
                        Los resultados aún no son visibles para el público. El enlace "Ver Resultados" está desactivado.
                    </p>
                </div>
                {{-- Asegúrate de que esta ruta exista en tus archivos de rutas de admin --}}
                <form action="{{ route('admin.encuestas.liberar', $encuesta) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type"submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Liberar Resultados Ahora
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