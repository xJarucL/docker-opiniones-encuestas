@extends('admin.admin-layout')

@section('title', 'Gestión de Encuestas')

{{-- Inclusión del CDN de SweetAlert2. --}}
@section('styles')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ... (tus estilos de alerta flotante) ... */
        #mensaje {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100%); z-index: 9999;
            min-width: 250px; max-width: 90%; text-align: center; padding: 12px 20px; border-radius: 8px;
            color: white; font-size: 0.875rem; font-weight: 500; box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            opacity: 0; animation: slideDown 0.5s forwards;
        }
        .success { background-color: #16a34a; }
        .error { background-color: #dc2626; }
        @keyframes slideDown { from { transform: translateX(-50%) translateY(-100%); opacity: 0; } to { transform: translateX(-50%) translateY(0); opacity: 1; } }
        @keyframes slideUp { from { transform: translateX(-50%) translateY(0); opacity: 1; } to { transform: translateX(-50%) translateY(-100%); opacity: 0; } }
    </style>
@endsection


@section('content')

    {{-- HEADER PRINCIPAL --}}
    <header class="mb-8 animate-fade-slide">
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Gestión de Encuestas</h1>
                    <p class="text-purple-100 text-lg">Administra, crea y edita las encuestas del sistema</p>
                </div>
                <a href="{{ route('admin.encuestas.create') }}" 
                    class="inline-flex items-center px-6 py-3 bg-white text-purple-700 font-semibold rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nueva Encuesta
                </a>
            </div>
        </div>
    </header>

    {{-- Contenedor de Alerta Flotante (x-msj-alert) --}}
    <div class="mb-6 animate-fade-slide relative z-50" style="animation-delay: 0.1s;">
        <x-msj-alert />
    </div>

    {{-- INICIO DEL CONTENEDOR DE LA TABLA Y LISTADO --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fade-slide" style="animation-delay: 0.2s;">
        
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center">
                    <div class="bg-purple-600 p-2 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Listado de Encuestas</h2>
                        <p class="text-sm text-gray-600">Total de encuestas: <span class="font-semibold text-purple-600">{{ $encuestas->total() }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha Creación</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($encuestas as $encuesta)
                        <tr class="hover:bg-purple-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">#{{ $encuesta->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $encuesta->titulo }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($encuesta->descripcion, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $encuesta->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            
                            {{-- ========================================================== --}}
                            {{-- AQUÍ ESTÁ EL CAMBIO PRINCIPAL: LÓGICA DE ESTADOS DINÁMICOS --}}
                            {{-- ========================================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($encuesta->status)
                                    @case('active')
                                        {{-- ACTIVA (Verde con punto pulsante) --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                            <span class="relative flex h-2 w-2 mr-2">
                                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                            Activa
                                        </span>
                                        @break

                                    @case('scheduled')
                                        {{-- PROGRAMADA (Amarillo) --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm" title="Inicia el {{ $encuesta->fecha_inicio->format('d/m/Y') }}">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Programada
                                            <span class="ml-1 text-[10px] opacity-75 hidden sm:inline">({{ $encuesta->fecha_inicio->format('d/m') }})</span>
                                        </span>
                                        @break

                                    @case('finished')
                                        {{-- FINALIZADA (Azul) --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Finalizada
                                        </span>
                                        @break

                                    @case('disabled')
                                        {{-- DESACTIVADA MANUALMENTE (Gris) --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            Inactiva
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            {{-- ========================================================== --}}
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ $encuesta->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.encuestas.show', $encuesta->id) }}" 
                                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 group"
                                        title="Ver encuesta">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.encuestas.edit', $encuesta->id) }}" 
                                        class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 group"
                                        title="Editar encuesta">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.encuestas.destroy', $encuesta->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-swal-form
                                                data-swal-title="¿Eliminar esta encuesta?"
                                                data-swal-text="Esta acción no se puede revertir. Se eliminarán todos los datos relacionados."
                                                data-swal-icon="warning"
                                                data-swal-confirm="Sí, eliminar"
                                                data-swal-cancel="Cancelar"
                                                data-swal-color="#e53e3e"
                                                class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 group"
                                                title="Eliminar encuesta">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        <button type="submit" class="hidden" data-swal-submit-button></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-6 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 text-lg font-medium mb-2">No hay encuestas registradas</p>
                                    <p class="text-gray-500 text-sm">Crea tu primera encuesta para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($encuestas->hasPages())
            <div class="px-6 py-4 bg-white border-t border-gray-200">
                {{ $encuestas->links() }}
            </div>
        @endif
    </div>
    {{-- FIN DEL CONTENEDOR DE LA TABLA Y LISTADO --}}

@endsection

@push('scripts')
{{-- SweetAlert2 Scripts y Lógica de Alerta Flotante --}}
<script>
    // Lógica para cerrar la alerta flotante (x-msj-alert)
    document.addEventListener('DOMContentLoaded', function () {
        const mensaje = document.getElementById('mensaje');
        
        if (mensaje && !mensaje.classList.contains('hidden') && (mensaje.textContent.trim().length > 0)) {
            setTimeout(() => {
                mensaje.style.animation = 'slideUp 0.5s forwards';
                setTimeout(() => {
                    mensaje.style.display = 'none';
                    mensaje.classList.add('hidden');
                }, 500);
            }, 4000);
        } else {
            if (mensaje) mensaje.style.display = 'none';
        }
    });
</script>
@endpush