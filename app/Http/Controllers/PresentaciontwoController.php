<?php

namespace App\Http\Controllers;

use App\Models\Encuesta; // <-- IMPORTANTE: Importa el modelo
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresentaciontwoController extends Controller
{
    /**
     * Revisa si los resultados de una encuesta son públicos.
     * Si no lo son, redirige con un error.
     */
    private function verificarAccesoPublico($encuestaId)
    {
        $encuesta = Encuesta::find($encuestaId);
        
        // Si no existe la encuesta O si los resultados NO están liberados
        if (!$encuesta || !$encuesta->resultados_publicos) {
            
            // Asumo que tienes una ruta 'inicio' para el home público
            return redirect()->route('inicio') 
                         ->with('error', 'Los resultados de esta encuesta aún no están disponibles.');
        }
        
        return $encuesta; // Devuelve la encuesta si todo está bien
    }

    /**
     * Validar si hay preguntas y redirigir apropiadamente
     */
    public function validar($encuestaId)
    {
        // <<< INICIO DE LA GUARDIA
        $encuesta = $this->verificarAccesoPublico($encuestaId);
        if ($encuesta instanceof \Illuminate\Http\RedirectResponse) {
            return $encuesta; // Redirige si no tiene acceso
        }
        // <<< FIN DE LA GUARDIA
        
        $totalPreguntas = DB::table('preguntas')
            ->where('encuesta_id', $encuestaId)
            ->count();

        // Corrección: Redirige a ruta pública 'inicio', no a 'encuestas.index'
        if ($totalPreguntas === 0) {
            return redirect()->route('inicio') 
                ->with('error', 'Esta encuesta no tiene preguntas disponibles.');
        }

        $primeraPregunta = Pregunta::where('encuesta_id', $encuestaId)
                                     ->orderBy('id', 'asc')
                                     ->first();

        // Si solo hay 1 pregunta, ir directo a votar (esta ruta debe ser pública)
        if ($totalPreguntas === 1) {
            return redirect()->route('presentacion', ['preguntaId' => $primeraPregunta->id]);
        }

        // Si hay múltiples preguntas, mostrar la pantalla de inicio
        return redirect()->route('presentacionone', ['encuestaId' => $encuestaId]);
    }

    public function index($encuestaId)
    {
        // <<< INICIO DE LA GUARDIA
        $encuesta = $this->verificarAccesoPublico($encuestaId);
        if ($encuesta instanceof \Illuminate\Http\RedirectResponse) {
            return $encuesta;
        }
        // <<< FIN DE LA GUARDIA

        $tituloEncuesta = $encuesta->titulo ?? 'Nominados';

        return view('users.presentacionone', compact('encuestaId', 'tituloEncuesta'));
    }

    public function inicio($encuestaId, $preguntaIndex)
    {
        // <<< INICIO DE LA GUARDIA
        $encuesta = $this->verificarAccesoPublico($encuestaId);
        if ($encuesta instanceof \Illuminate\Http\RedirectResponse) {
            return $encuesta;
        }
        // <<< FIN DE LA GUARDIA

        $preguntas = DB::table('preguntas')
            ->where('encuesta_id', $encuestaId)
            ->orderBy('id')
            ->get();

        if ($preguntas->isEmpty()) {
            return redirect()->route('resultadostwo', $encuestaId);
        }

        $preguntaActual = $preguntas[$preguntaIndex] ?? null;

        if (!$preguntaActual) {
            return redirect()->route('resultadostwo', $encuestaId);
        }

        $tituloEncuesta = $preguntaActual->texto ?? 'Pregunta';

        return view('users.presentaciontwo', compact('encuestaId', 'preguntaIndex', 'tituloEncuesta'));
    }

    public function podio($encuestaId, $preguntaIndex)
    {
        // <<< INICIO DE LA GUARDIA
        $encuesta = $this->verificarAccesoPublico($encuestaId);
        if ($encuesta instanceof \Illuminate\Http\RedirectResponse) {
            return $encuesta;
        }
        // <<< FIN DE LA GUARDIA

        $preguntas = DB::table('preguntas')
            ->where('encuesta_id', $encuestaId)
            ->orderBy('id')
            ->get();

        if (!isset($preguntas[$preguntaIndex])) {
            return redirect()->route('resultadostwo', $encuestaId);
        }

        $pregunta = $preguntas[$preguntaIndex];
        
        // --- (Tu lógica de $totalVotos, $topRespuestas, $resultados... no cambia) ---
        $totalVotos = DB::table('respuestas')
            ->where('pregunta_id', $pregunta->id)
            ->whereNotNull('respuesta')
            ->where('respuesta', '!=', '')
            ->count();

        $topRespuestas = DB::table('respuestas')
            ->select('respuesta', DB::raw('COUNT(*) as total'))
            ->where('pregunta_id', $pregunta->id)
            ->whereNotNull('respuesta')
            ->where('respuesta', '!=', '')
            ->groupBy('respuesta')
            ->orderBy('total', 'DESC')
            ->limit(3)
            ->get();

        $resultados = $topRespuestas->map(function($item) use ($totalVotos) {
            $item->porcentaje = $totalVotos > 0 
                ? round(($item->total / $totalVotos) * 100, 2) 
                : 0;
            return $item;
        })->values()->all();

        $hayMasPreguntas = ($preguntaIndex + 1) < $preguntas->count();
        
        return view('users.podiotwo', compact(
            'encuestaId',
            'preguntaIndex',
            'pregunta',
            'resultados',
            'hayMasPreguntas'
        ));
    }

    public function resultados($encuestaId)
    {
        // <<< INICIO DE LA GUARDIA
        $encuesta = $this->verificarAccesoPublico($encuestaId);
        if ($encuesta instanceof \Illuminate\Http\RedirectResponse) {
            return $encuesta;
        }
        // <<< FIN DE LA GUARDIA

        $tituloEncuesta = $encuesta->titulo ?? 'Resultados';

        // --- (Tu consulta $allResultados no cambia) ---
        $allResultados = DB::select("
            SELECT 
                p.id AS pregunta_id, p.texto AS pregunta, r.respuesta AS opcion,
                COUNT(r.id) AS total_votos,
                ROUND(
                    COUNT(r.id) * 100.0 / NULLIF(
                        (SELECT COUNT(*) 
                         FROM respuestas r2 
                         WHERE r2.pregunta_id = p.id
                           AND r2.respuesta IS NOT NULL 
                           AND r2.respuesta != ''), 
                        0
                    ), 2
                ) AS porcentaje
            FROM preguntas p
            LEFT JOIN respuestas r ON p.id = r.pregunta_id
            WHERE p.encuesta_id = ?
              AND r.respuesta IS NOT NULL
              AND r.respuesta != ''
            GROUP BY p.id, p.texto, r.respuesta
            ORDER BY p.id, total_votos DESC
        ", [$encuestaId]);

        $resultados = collect($allResultados)->groupBy('pregunta_id');

        // --- INICIO DE LA CORRECCIÓN ---
        
        // 1. Buscar la primera pregunta de esta encuesta ordenando por ID
        $primeraPregunta = DB::table('preguntas')
                            ->where('encuesta_id', $encuestaId)
                            ->orderBy('id', 'asc') // <-- CORREGIDO
                            ->first();
        
        // 2. Definir la variable (será null si no hay preguntas)
        $primeraPreguntaId = $primeraPregunta ? $primeraPregunta->id : null;
        
        // --- FIN DE LA CORRECCIÓN ---

        // 3. Pasar la variable a la vista
        return view('users.resultadostwo', compact(
            'resultados', 
            'tituloEncuesta', 
            'encuestaId',
            'primeraPreguntaId' // <-- LA VARIABLE AHORA SE ESTÁ ENVIANDO
        ));
    }
}