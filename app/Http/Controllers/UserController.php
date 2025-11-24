<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tipo_usuario;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service){
        $this->service = $service;
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = User::where('email', $request->email)->first();

        if ($usuario && Hash::check($request->password, $usuario->password)) {
            Auth::login($usuario);

            return response()->json([
                'mensaje' => '¡Inicio de sesión exitoso!',
                'ruta' => route('inicio'),
                'class' => 'success'
            ]);
        }

        return response()->json([
            'mensaje' => 'Credenciales incorrectas.',
            'class' => 'error'
        ], 422);
    }

    public function dashboard(){
        $usuario = auth()->user();

        if ($usuario->fk_tipo_user == 1) {
            return view('admin.dashboard', [
                'usuario' => $usuario,
                'totalUsuarios' => User::count(),
                'totalComentarios' => Comentario::count(),
                'totalEncuestas' => DB::table('encuestas')->count()
            ]);
        }

        return view('users.dashboard', compact('usuario'));
    }

    public function listaUsuarios(){
        $usuarios = User::with('tipo_usuario')->paginate(10);
        $tipos_usuario = Tipo_usuario::all();

        return view('users.listado', compact('usuarios', 'tipos_usuario'));
    }

    public function cambiarTipo(Request $request, $id){
        $request->validate([
            'fk_tipo_user' => 'required|exists:tipo_user,pk_tipo_user',
        ]);

        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->fk_tipo_user = $request->fk_tipo_user;
        $usuario->save();

        return back()->with('success', 'Tipo de usuario actualizado correctamente.');
    }

    public function eliminar($id){
        try {
            $this->service->deleteUser($id);

            return redirect()->route('admin.usuarios.lista')
                            ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function restaurar($id){
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()
            ->route('admin.usuarios.inactivos')
            ->with('success', 'Usuario restaurado correctamente.');
    }

    public function listaUsuarios_inactivos(){
        $usuarios = User::onlyTrashed()->with('tipo_usuario')->paginate(10);
        $tipos_usuario = Tipo_usuario::all();

        return view('users.listado', compact('usuarios', 'tipos_usuario'));
    }

    public function guardarUsuario(Request $request){
        $isEdit = $request->filled('id');

        $emailRule = $isEdit
            ? 'required|email|unique:users,email,' . $request->id . ',pk_usuario'
            : 'required|email|unique:users,email';

        $reglas = [
            'username'   => 'required|string|max:255',
            'nombres'    => 'required|string|max:255',
            'ap_paterno' => 'required|string|max:255',
            'ap_materno' => 'nullable|string|max:255',
            'email'      => $emailRule,
            'img_user'   => 'nullable|image|max:2048',
        ];

        if (!$isEdit || $request->filled('password')) {
            $reglas['password'] = 'required|string|min:6';
        }

        $validator = Validator::make($request->all(), $reglas);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => $validator->errors(),
                'class' => 'error'
            ], 422);
        }

        $data = $request->all();
        $file = $request->file('img_user');

        try {
            $usuario = null;
            if ($isEdit) {
                $usuario = User::findOrFail($request->id);
            }

            $this->service->saveModel($data, $usuario, $file);

            return response()->json([
                'mensaje' => $isEdit ? 'Usuario actualizado correctamente.' : 'Usuario registrado correctamente.',
                'class' => 'success',
                'ruta' => route('admin.usuarios.lista')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al guardar el usuario: ' . $e->getMessage(),
                'class' => 'error'
            ], 422);
        }
    }


    public function edit($id){
        $usuario = User::findOrFail($id);
        return view('users.formulario', compact('usuario'));
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Cerraste sesión correctamente.');
    }

    public function perfil(){
        $usuario = auth()->user();

        $comentarios = Comentario::with(['autor', 'respuestas.autor'])
            ->where('fk_perfil_user', $usuario->pk_usuario)
            ->whereIn('estatus', ['visible', 'oculto'])
            ->whereNull('fk_coment_respuesta')
            ->orderByDesc('fecha_creacion')
            ->get();

        return view('users.perfil', compact('usuario', 'comentarios'));
    }

    public function listarCompañeros(){
        $usuarios = User::where('estatus', true)
            ->where('fk_tipo_user', 2)
            ->get();

        return view('users.compañeros', compact('usuarios'));
    }

    public function mostrarCompañero($id){
        $usuario = User::findOrFail($id);

        $comentarios = Comentario::with(['autor', 'respuestas.autor'])
            ->where('fk_perfil_user', $usuario->pk_usuario)
            ->whereIn('estatus', ['visible', 'oculto'])
            ->whereNull('fk_coment_respuesta')
            ->orderByDesc('fecha_creacion')
            ->get();

        return view('users.perfil', compact('usuario', 'comentarios'));
    }
}
