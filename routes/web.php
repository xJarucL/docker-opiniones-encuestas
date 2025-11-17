<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PresentacionController;
use App\Http\Controllers\PresentaciontwoController;
use App\Http\Controllers\DiagnosticoController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UsuarioMiddleware;

// ==========================================================
// RUTAS PÚBLICAS (GUEST)
// ==========================================================
Route::get('/', fn() => view('login'))->name('login');
Route::post('/iniciando_sesion', [UserController::class, 'login'])->name('iniciando');

Route::get('/recuperar_contraseña', fn() => view('passwordrecovery.recuperar_contraseña'))
    ->name('recuperar_contraseña');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->name('password.update');

// ==========================================================
// RUTAS AUTENTICADAS
// ==========================================================
Route::middleware(['auth'])->group(function () {

    // PRESENTACIÓN Y RESULTADOS
    Route::get('/presentacion/{preguntaId}', [PresentacionController::class, 'index'])->name('presentacion');
    Route::post('/presentacion/store', [PresentacionController::class, 'store'])->name('presentacion.store');
    Route::get('/podio/{preguntaId}', [PresentacionController::class, 'podio'])->name('podio');
    Route::get('/resultados/{preguntaId}', [PresentacionController::class, 'resultados'])->name('resultados');

    // PRESENTACIÓN COMPLETA CON ANIMACIONES
    Route::get('/presentacion-completa/{encuestaId}/{preguntaId}',
        [PresentacionController::class, 'mostrarCompleta']
    )->name('presentacion.completa');

    // PRESENTACIÓN MÚLTIPLE
    Route::get('/presentacionone/{encuestaId}', [PresentaciontwoController::class, 'index'])->name('presentacionone');
    Route::get('/presentaciontwo/{encuestaId}/{preguntaIndex}', [PresentaciontwoController::class, 'inicio'])->name('presentaciontwo.inicio');
    Route::get('/podiotwo/{encuestaId}/{preguntaIndex}', [PresentaciontwoController::class, 'podio'])->name('podiotwo');
    Route::get('/resultadostwo/{encuestaId}', [PresentaciontwoController::class, 'resultados'])->name('resultadostwo');
    Route::get('/validarButton/{encuestaId}', [PresentaciontwoController::class, 'validar'])->name('validarButton');

    // DASHBOARD Y PERFIL
    // Esta es ahora la ÚNICA ruta de dashboard.
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('inicio'); 
    
    Route::get('/perfil', [UserController::class, 'perfil'])->name('perfil');
    Route::get('/usuarios/editar/{id}', [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/actualizar/{id}', [UserController::class, 'guardarUsuario'])->name('usuarios.update');

    // LOGOUT
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // COMENTARIOS
    Route::prefix('comentarios')->group(function () {
        Route::post('/usuarios/compañero/{id}', [ComentarioController::class, 'store'])->name('comentarios.store');
        Route::post('/{comentario}/responder', [ComentarioController::class, 'reply'])->name('comentarios.reply');
        Route::delete('/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
        Route::patch('/{comentario}/editar', [ComentarioController::class, 'update'])->name('comentarios.update');
    });

    // COMPAÑEROS Y ENCUESTAS
    Route::get('/usuarios/compañeros', [UserController::class, 'listarCompañeros'])->name('compañeros');
    Route::get('/usuarios/compañero/{id}', [UserController::class, 'mostrarCompañero'])->name('compañero.show');
    Route::get('/encuestas', [EncuestaController::class, 'showPublicIndex'])->name('encuestas.index');

    // ==========================================================
    // RUTAS DE ADMINISTRADOR
    // ==========================================================
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {

        // COMENTARIOS
        Route::prefix('comentarios')->name('comentarios.')->group(function () {
            Route::get('/', [ComentarioController::class, 'index'])->name('index');
            Route::patch('/{comentario}/ocultar', [ComentarioController::class, 'hide'])->name('hide');
            Route::patch('/{comentario}/mostrar', [ComentarioController::class, 'showComment'])->name('show');
            Route::delete('/{comentario}', [ComentarioController::class, 'destroyAdmin'])->name('destroy');
        });

        // <<< LA RUTA DE LIBERAR SE MOVIÓ DE AQUÍ >>>

        // ENCUESTAS
        Route::prefix('encuestas')->name('encuestas.')->group(function () {
            Route::get('/', [EncuestaController::class, 'index'])->name('index');
            Route::get('/crear', [EncuestaController::class, 'create'])->name('create');
            Route::post('/guardar', [EncuestaController::class, 'store'])->name('store');
            Route::get('/{id}', [EncuestaController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [EncuestaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [EncuestaController::class, 'update'])->name('update');
            Route::delete('/{id}', [EncuestaController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/estado', [EncuestaController::class, 'cambiarEstado'])->name('cambiar-estado');

            // <<< CORRECCIÓN: RUTA AÑADIDA AQUÍ >>>
            // Esta ruta ahora hereda el prefijo /admin/encuestas y el nombre admin.encuestas.
            // (Usando {encuesta} para que coincida con la variable de la vista)
            Route::patch('/{encuesta}/liberar-resultados', [EncuestaController::class, 'liberarResultados'])
                 ->name('liberar'); // El nombre final será 'admin.encuestas.liberar'
        });

        // CATEGORÍAS
        Route::prefix('categorias')->name('categorias.')->group(function () {
            Route::get('/', [CategoriaController::class, 'index'])->name('index');
            Route::post('/guardar', [CategoriaController::class, 'store'])->name('store');
            Route::put('/{id}', [CategoriaController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoriaController::class, 'destroy'])->name('destroy');
        });

        // DIAGNÓSTICO
        Route::prefix('diagnostico')->name('diagnostico.')->group(function () {
            Route::get('/{encuestaId}', [DiagnosticoController::class, 'diagnosticar'])->name('ver');
            Route::post('/{encuestaId}/corregir', [DiagnosticoController::class, 'corregirRespuestasVacias'])->name('corregir');
        });

        // USUARIOS
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UserController::class, 'listaUsuarios'])->name('lista');
            Route::get('/inactivos', [UserController::class, 'listaUsuarios_inactivos'])->name('inactivos');

            Route::get('/registro', function () {
                $tipos_usuario = \App\Models\Tipo_usuario::all();
                return view('users.formulario', ['usuario' => null, 'tipos_usuario' => $tipos_usuario]);
            })->name('registro');

            Route::post('/guardar', [UserController::class, 'guardarUsuario'])->name('guardar');
            Route::put('/cambiar-tipo/{id}', [UserController::class, 'cambiarTipo'])->name('cambiar-tipo');
            Route::delete('/eliminar/{id}', [UserController::class, 'eliminar'])->name('eliminar');
            Route::post('/restaurar/{id}', [UserController::class, 'restaurar'])->name('restaurar');
            Route::delete('/eliminar-permanente/{id}', [UserController::class, 'eliminarPermanente'])->name('eliminar-permanente');
        });
    });
});