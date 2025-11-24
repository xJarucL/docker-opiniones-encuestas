<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\Categoria;
use App\Models\Pregunta;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    public function liberarResultados(Encuesta $encuesta)
    {
        $encuesta->update(['resultados_publicos' => true]);

        return back()->with('success', '¡Resultados liberados! El público ya puede verlos.');
    }
    
    /**
     * Vista pública de encuestas
     */
/**
     * Vista pública de encuestas
     */
    public function showPublicIndex()
    {
        // USAMOS EL NUEVO SCOPE 'publicas()'
        // Esto oculta automáticamente las programadas o finalizadas
        $encuestas = Encuesta::publicas() 
                            ->with('preguntas') 
                            ->latest()
                            ->paginate(10);
        
        $usuario = Auth::user();
        $userId = $usuario->pk_usuario;

        // (Tu lógica de encuestas respondidas sigue igual...)
        $encuestasRespondidas = DB::table('respuestas')
            ->join('preguntas', 'respuestas.pregunta_id', '=', 'preguntas.id')
            ->where('respuestas.user_id', $userId) 
            ->select('preguntas.encuesta_id')
            ->distinct()
            ->pluck('encuesta_id')
            ->flip(); 
                            
        return view('users.encuestas', [
            'encuestas' => $encuestas,
            'usuario' => $usuario,
            'encuestasRespondidas' => $encuestasRespondidas
        ]);
    }


    // ==========================================================
    // FUNCIONES DE ADMINISTRADOR
    // ==========================================================

    public function index()
    {
        $encuestas = Encuesta::with('categoria')
            ->withCount('preguntas')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalRespuestas = 0; // Esto necesitará una lógica más avanzada
        $totalCategorias = Categoria::count();

        return view('admin.encuestas.index', compact('encuestas', 'totalRespuestas', 'totalCategorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.encuestas.create', compact('categorias'));
    }

    /**
     * Guardar nueva encuesta
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            
            'preguntas' => 'required|array|min:1',
            'preguntas.*.texto' => 'required|string',
            'preguntas.*.tipo' => 'required|in:nominados', 
            
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ], [
            'titulo.required' => 'El título es obligatorio',
            'categoria_id.required' => 'Debes seleccionar una categoría',
            'preguntas.required' => 'Debes agregar al menos una pregunta',
            'preguntas.*.texto.required' => 'El texto de la pregunta es obligatorio',
        ]);

        $encuesta = Encuesta::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'categoria_id' => $request->categoria_id,
            'estado' => 1, 
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        // Crear las preguntas
        foreach ($request->preguntas as $index => $preguntaData) {
            Pregunta::create([
                'encuesta_id' => $encuesta->id,
                'texto' => $preguntaData['texto'],
                'tipo' => $preguntaData['tipo'], 
                'orden' => $index,
                'opciones' => json_encode([]), 
            ]);
        }

        return redirect()->route('admin.encuestas.index')
            ->with('success', 'Encuesta creada exitosamente');
    }

    /**
     * Mostrar resultados de una encuesta
     */
public function show($id)
    {
        // Cargamos la encuesta
        $encuesta = Encuesta::with(['categoria', 'preguntas.respuestas'])->findOrFail($id);
        
        // 1. Obtener IDs de las preguntas de esta encuesta
        $preguntaIds = $encuesta->preguntas->pluck('id');

        // 2. Contar usuarios ÚNICOS que han respondido (evita contar doble si responden varias preguntas)
        $usuariosParticipantes = DB::table('respuestas')
            ->whereIn('pregunta_id', $preguntaIds)
            ->distinct('user_id')
            ->count();

        // 3. Contar total de usuarios en el sistema (o el grupo objetivo)
        $totalUsuarios = User::count();

        // 4. Calcular pendientes y porcentaje
        $usuariosPendientes = $totalUsuarios - $usuariosParticipantes;
        $porcentajeParticipacion = $totalUsuarios > 0 ? ($usuariosParticipantes / $totalUsuarios) * 100 : 0;

        // Variables antiguas (por si las usas en otro lado)
        $totalRespuestas = DB::table('respuestas')->whereIn('pregunta_id', $preguntaIds)->count();

        return view('admin.encuestas.show', compact(
            'encuesta', 
            'totalRespuestas', 
            'usuariosParticipantes', 
            'usuariosPendientes', 
            'totalUsuarios',
            'porcentajeParticipacion'
        ));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $encuesta = Encuesta::with('preguntas')->findOrFail($id);
        $categorias = Categoria::all();
        
        return view('admin.encuestas.create', compact('encuesta', 'categorias'));
    }

    /**
     * Actualizar encuesta
     */
    public function update(Request $request, $id)
    {
        $encuesta = Encuesta::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            
            'preguntas' => 'required|array|min:1',
            'preguntas.*.texto' => 'required|string',
            'preguntas.*.tipo' => 'required|in:nominados', 
            
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Actualizar encuesta
        $encuesta->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'categoria_id' => $request->categoria_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        // Eliminar preguntas antiguas y crear nuevas
        $encuesta->preguntas()->delete();

        foreach ($request->preguntas as $index => $preguntaData) {
            Pregunta::create([
                'encuesta_id' => $encuesta->id, 
                'texto' => $preguntaData['texto'],
                'tipo' => $preguntaData['tipo'],
                'orden' => $index,
                'opciones' => json_encode([]), 
            ]);
        }

        return redirect()->route('admin.encuestas.index')
            ->with('success', 'Encuesta actualizada exitosamente');
    }

    /**
     * Eliminar encuesta
     */
    public function destroy($id)
    {
        $encuesta = Encuesta::findOrFail($id);
        $encuesta->delete();

        return redirect()->route('admin.encuestas.index')
            ->with('success', 'Encuesta eliminada exitosamente');
    }

    /**
     * Cambiar estado de encuesta (activa/inactiva)
     */
    public function cambiarEstado($id)
    {
        $encuesta = Encuesta::findOrFail($id);
        $encuesta->estado = !$encuesta->estado;
        $encuesta->save();

        $mensaje = $encuesta->estado ? 'Encuesta activada manualmente' : 'Encuesta desactivada manualmente';

        return redirect()->route('admin.encuestas.index')
            ->with('success', $mensaje);
    }
}