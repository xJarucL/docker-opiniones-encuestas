<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ComentarioController extends Controller
{
    
    
    use AuthorizesRequests;
    
    public function store(Request $r, $id)
    {
        $r->validate([
            'contenido' => 'required|string|max:1000',
            'anonimo' => 'nullable|boolean',
        ]);

        $perfil = User::findOrFail($id);

        Comentario::create([
            'fk_autor' => auth()->user()->pk_usuario,
            'fk_perfil_user' => $perfil->pk_usuario,
            'contenido' => $r->contenido,
            'anonimo' => (bool)$r->anonimo,
            'estatus' => 'visible',
        ]);

        return back()->with('success', 'Comentario publicado correctamente.');
    }
    /**
 * Elimina un comentario (función de administrador).
 */
public function destroyAdmin(Comentario $comentario)
{
    // Laravel encuentra el comentario automáticamente gracias al route model binding.
    // Si tu modelo 'Comentario' usa SoftDeletes, esto será un borrado lógico.
    $comentario->delete();

    return redirect()->route('admin.comentarios.index')->with('success', 'Comentario eliminado correctamente.');
}
        /**
 * Vuelve a mostrar un comentario que estaba oculto.
 */
public function showComment(Comentario $comentario)
{
    // Laravel encuentra el comentario automáticamente gracias al route model binding.
    // Asumiendo que 1 = 'Visible' (según tu vista de admin/comentarios/index.blade.php)
    $comentario->estatus = 1;
    $comentario->save();

    return redirect()->route('admin.comentarios.index')->with('success', 'El comentario ahora está visible.');
}

    public function reply(Request $r, Comentario $comentario)
    {
        $r->validate(['contenido' => 'required|string|max:1000']);

        Comentario::create([
            'fk_autor' => auth()->user()->pk_usuario,
            'fk_perfil_user' => $comentario->fk_perfil_user,
            'fk_coment_respuesta' => $comentario->pk_comentario,
            'contenido' => $r->contenido,
            'anonimo' => false,
            'estatus' => 'visible',
        ]);

        return back()->with('success', 'Respuesta publicada correctamente.');
    }

    public function destroy(Comentario $comentario)
    {
        $this->authorize('delete', $comentario);
        $comentario->update(['estatus' => 'eliminado']);
        $comentario->delete();
        return back()->with('success', 'Comentario eliminado.');
    }

    public function hide(Comentario $comentario)
    {
        Gate::authorize('moderate-comments');
        $comentario->update(['estatus' => 'oculto']);
        return back()->with('success', 'Comentario oculto.');
    }

    public function show(Comentario $comentario)
    {
        Gate::authorize('moderate-comments');
        $comentario->update(['estatus' => 'visible']);
        return back()->with('success', 'Comentario visible.');
    }
    public function index()
{
    // Obtenemos todos los comentarios.
    // Es buena práctica cargar relaciones (como el usuario que lo escribió)
    // y paginar los resultados para un panel de admin.
    $comentarios = Comentario::with('user') // Carga la relación 'user'
                            ->latest()      // Ordena del más nuevo al más viejo
                            ->paginate(20);  // Muestra 20 por página

    // Devuelve la vista de admin, pasándole los comentarios
    return view('admin.comentarios.index', [
        'comentarios' => $comentarios
    ]);
}
}
