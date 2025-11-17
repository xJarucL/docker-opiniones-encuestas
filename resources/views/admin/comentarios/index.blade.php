@extends('admin.admin-layout')

@section('title', 'Gestión de Comentarios')

@section('content')

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            {{-- CORREGIDO: El enlace ahora apunta a 'inicio' --}}
            <a href="{{ route('inicio') }}" class="text-purple-600 hover:text-purple-700 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-2">Gestión de Comentarios</h1>
            <p class="text-gray-600 mt-1">Modera y administra todos los comentarios del sistema.</p>
        </div>
        {{-- (El botón de 'nuevo' no aplica aquí) --}}
    </div>
</div>

{{-- Alerta del componente --}}
<div class="mb-6">
    <x-msj-alert />
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-purple-100 text-sm mb-1">Total Comentarios</p>
                <p class="text-3xl font-bold">{{ $comentarios->total() }}</p>
            </div>
            <svg class="w-10 h-10 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.934L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </div>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-indigo-100 text-sm mb-1">Comentarios Visibles</p>
                <p class="text-3xl font-bold">{{ $comentariosVisibles ?? 'N/A' }}</p>
            </div>
            <svg class="w-10 h-10 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.97 3.031-4.27 5.7-8.15 6.222C6.88 18.92 3.06 16.3 2.458 12z"></path>
            </svg>
        </div>
    </div>
</div>

@if ($comentarios->isEmpty())
    <div class="col-span-full bg-white rounded-xl shadow-lg text-center py-16 px-6">
        <div class="max-w-md mx-auto">
            <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.934L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay comentarios</h3>
            <p class="text-gray-600 mb-6">Cuando los usuarios empiecen a comentar, aparecerán aquí para que los puedas moderar.</p>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 text-left text-sm font-semibold text-gray-600 uppercase">Autor</th>
                    <th class="p-4 text-left text-sm font-semibold text-gray-600 uppercase">Comentario</th>
                    <th class="p-4 text-left text-sm font-semibold text-gray-600 uppercase">En Perfil de</th>
                    <th class="p-4 text-left text-sm font-semibold text-gray-600 uppercase">Fecha</th>
                    <th class="p-4 text-left text-sm font-semibold text-gray-600 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($comentarios as $comentario)
                    <tr class="hover:bg-gray-50 animate-fade-slide" style="animation-delay: {{ $loop->index * 0.03 }}s;">
                        <td class="p-4 align-top">
                            <div class="font-medium text-gray-800">{{ $comentario->autor->username ?? 'Usuario no encontrado' }}</div>
                            <div class="text-sm text-gray-500">{{ $comentario->autor->email ?? 'Sin email' }}</div>
                        </td>
                        <td class="p-4 align-top max-w-sm">
                            <p class="text-gray-700 text-sm break-words">{{ $comentario->contenido }}</p>
                            @if($comentario->padre)
                                <span class="text-xs text-purple-600 mt-1 block">En respuesta a Comentario #{{ $comentario->fk_coment_respuesta }}</span>
                            @endif
                        </td>
                        <td class="p-4 align-top">
                            <div class="font-medium text-gray-800">{{ $comentario->perfil->username ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $comentario->fk_perfil_user }}</div>
                        </td>
                        <td class="p-4 align-top text-sm text-gray-600">
                            {{ $comentario->fecha_creacion->format('d/m/Y H:i') }}
                        </td>
                        
                        <td class="p-4 align-top">
                            <div class="flex gap-2">
                                {{-- CORREGIDO: Formulario de "Eliminar" ahora usa el botón oculto --}}
                                <form action="{{ route('admin.comentarios.destroy', $comentario->pk_comentario) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            data-swal-form
                                            data-swal-title="¿Eliminar Comentario?"
                                            data-swal-text="Esta acción eliminará el comentario permanentemente. (Soft Delete)"
                                            data-swal-icon="warning"
                                            data-swal-confirm="Sí, eliminar"
                                            data-swal-cancel="Cancelar"
                                            data-swal-color="#dc2626"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                            title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <button type="submit" class="hidden" data-swal-submit-button></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if ($comentarios->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $comentarios->links() }}
            </div>
        @endif
    </div>
@endif

@endsection

{{-- CORREGIDO: Eliminado el @push('scripts') duplicado que estaba aquí --}}