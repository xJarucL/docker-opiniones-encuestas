<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\User;
use App\Models\Encuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresentacionController extends Controller
{
    /**
     * ------------------------------------------------------------------
     *  FUNCIÓN PARA VERIFICAR SI LOS RESULTADOS SON PÚBLICOS
     * ------------------------------------------------------------------
     */
    private function verificarAccesoPublico($preguntaId)
    {
        $pregunta = Pregunta::find($preguntaId);
        if (!$pregunta) {
            abort(404);
        }

        $encuesta = $pregunta->encuesta;

        if (!$encuesta || !$encuesta->resultados_publicos) {
            return redirect()->route('inicio')
                ->with('error', 'Los resultados de esta encuesta aún no están disponibles.');
        }

        return ['pregunta' => $pregunta, 'encuesta' => $encuesta];
    }


    /**
     * ------------------------------------------------------------------
     *  MOSTRAR LA PREGUNTA PARA VOTAR
     * ------------------------------------------------------------------
     */
    public function index($preguntaId)
    {
        $pregunta = Pregunta::findOrFail($preguntaId);

        $opciones = [];

        // Tipo nominados → cargar usuarios como opciones
        if ($pregunta->tipo === 'nominados') {

            $users = User::orderBy('nombres', 'asc')->get();
            $opciones = $users->pluck('nombres')->toArray();

        } else {

            $opcionesJSON = json_decode($pregunta->opciones, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($opcionesJSON)) {
                $opciones = $opcionesJSON;
            }
        }

        $userId = Auth::id();
        $haVotado = false;

        if ($userId) {
            $haVotado = Respuesta::where('pregunta_id', $preguntaId)
                                 ->where('user_id', $userId)
                                 ->exists();
        }

        $pregunta->load('encuesta');

        return view('presentacion.index', [
            'pregunta' => $pregunta,
            'opciones' => $opciones,
            'haVotado' => $haVotado, 
        ]);
    }


    /**
     * ------------------------------------------------------------------
     *  VISTA COMPLETA (TOP 3)
     * ------------------------------------------------------------------
     */
    public function mostrarCompleta($encuestaId, $preguntaId)
    {
        $encuesta = Encuesta::findOrFail($encuestaId);
        $pregunta = Pregunta::findOrFail($preguntaId);
        
        $resultados = DB::select("
            SELECT 
                respuesta as opcion,
                COUNT(*) as votos,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas WHERE pregunta_id = ?), 2) as porcentaje
            FROM respuestas
            WHERE pregunta_id = ?
            GROUP BY respuesta
            ORDER BY votos DESC
            LIMIT 3
        ", [$preguntaId, $preguntaId]);
        
        return view('presentacion-completa', [
            'tituloEncuesta' => $pregunta->texto,
            'resultados' => $resultados,
            'preguntaId' => $preguntaId
        ]);
    }


    /**
     * ------------------------------------------------------------------
     *  GUARDAR RESPUESTA
     * ------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $request->validate([
            'pregunta_id' => 'required|exists:preguntas,id',
            'respuesta' => 'required|string',
        ]);

        $pregunta = Pregunta::findOrFail($request->pregunta_id);

        $votoExistente = Respuesta::where('pregunta_id', $pregunta->id)
                                  ->where('user_id', Auth::id())
                                  ->exists();

        if (!$votoExistente) {
            Respuesta::create([
                'pregunta_id' => $pregunta->id,
                'user_id' => Auth::id(),
                'respuesta' => $request->respuesta,
                'encuesta_id' => $pregunta->encuesta_id,
            ]);
        }

        // Buscar siguiente pregunta
        $siguientePregunta = Pregunta::where('encuesta_id', $pregunta->encuesta_id)
                                    ->where('orden', '>', $pregunta->orden)
                                    ->orderBy('orden', 'asc')
                                    ->first();

        if ($siguientePregunta) {
            return redirect()->route('presentacion', [
                'preguntaId' => $siguientePregunta->id
            ]);
        }

        return redirect()->route('encuestas.index')
            ->with('survey_completed', '¡Encuesta completada! Muchas gracias por participar.');
    }


    /**
     * ------------------------------------------------------------------
     *  PODIO (TOP 3)
     * ------------------------------------------------------------------
     */
    public function podio($preguntaId)
    {
        $verificacion = $this->verificarAccesoPublico($preguntaId);
        if ($verificacion instanceof \Illuminate\Http\RedirectResponse) {
            return $verificacion;
        }

        $pregunta = $verificacion['pregunta'];

        $resultados = DB::select("
            SELECT 
                respuesta AS nombre, 
                COUNT(*) AS total_votos 
            FROM respuestas 
            WHERE pregunta_id = ? 
            GROUP BY respuesta 
            ORDER BY total_votos DESC
            LIMIT 3
        ", [$preguntaId]);

        $podio = [
            'primero' => $resultados[0] ?? null,
            'segundo' => $resultados[1] ?? null,
            'tercero' => $resultados[2] ?? null,
        ];

        return view('users.podio', compact('podio', 'preguntaId', 'pregunta'));
    }


    /**
     * ------------------------------------------------------------------
     *  RESULTADOS COMPLETOS
     * ------------------------------------------------------------------
     */
    public function resultados($preguntaId)
    {
        $verificacion = $this->verificarAccesoPublico($preguntaId);
        if ($verificacion instanceof \Illuminate\Http\RedirectResponse) {
            return $verificacion;
        }

        $pregunta = Pregunta::findOrFail($preguntaId);

        $encuesta = DB::select("
            SELECT e.titulo 
            FROM encuestas e
            INNER JOIN preguntas p ON e.id = p.encuesta_id
            WHERE p.id = ?
            LIMIT 1
        ", [$preguntaId]);

        $tituloEncuesta = $encuesta[0]->titulo ?? 'Resultados';

        $resultados = DB::select("
            SELECT 
                respuesta AS nombre, 
                COUNT(*) AS total_votos, 
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas WHERE pregunta_id = ?)), 2) AS porcentaje 
            FROM respuestas 
            WHERE pregunta_id = ? 
            GROUP BY respuesta
            ORDER BY total_votos DESC
        ", [$preguntaId, $preguntaId]);

        $totalParticipantes = DB::table('respuestas')
            ->where('pregunta_id', $preguntaId)
            ->count();

        return view('users.resultados', compact(
            'resultados',
            'tituloEncuesta',
            'totalParticipantes',
            'preguntaId'
        ));
    }
}
