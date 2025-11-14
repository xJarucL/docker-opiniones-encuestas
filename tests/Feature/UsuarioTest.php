<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tipo_usuario;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    protected $adminTipo;
    protected $alumnoTipo;

    // En esta parte, se preparan los tipo de usuario para hacer inserciones de usuarios
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TipoUsuarioSeeder::class);

        $this->adminTipo = Tipo_usuario::where('nombre', 'Administrador')->first();
        $this->alumnoTipo = Tipo_usuario::where('nombre', 'Común')->first();
    }

    // Esta prueba debe crear un usuario e intentar iniciar sesión mandando la información al endpoint de inicio,
    // si todo sale bien, espera la respuesta del controlador, la cual, se retorna en JSON.

    /** @test */
    public function login()
    {
        // Crea un usuario nuevo
        $user = User::create([
            'username' => 'testuser',
            'nombres' => 'Test',
            'ap_paterno' => 'Usuario',
            'ap_materno' => 'Prueba',
            'email' => 'test@correo.com',
            'password' => Hash::make('test'),
            'fk_tipo_user' => $this->adminTipo->pk_tipo_user,
        ]);

        // Manda las credenciales a la ruta de iniciar sesión
        $response = $this->post('/iniciando_sesion', [
            'email' => 'test@correo.com',
            'password' => 'test',
        ]);

        // Espera que la respuesta del servidor sea esta:
        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => '¡Inicio de sesión exitoso!',
                     'class' => 'success',
                     'ruta' => route('inicio')
                 ]);

        $this->assertAuthenticatedAs($user);
    }

    // Esta prueba es para validar la seguridad de rutas de usuario,
    // en este caso, valida que un usuario autenticado pueda acceder al inicio (dashboard)

    /** @test */
    public function usuarioAutenticadoEnrutamiento(){
        // Crea un usuario nuevo
        $user = User::create([
            'username' => 'jarucl',
            'nombres' => 'Jaruny',
            'ap_paterno' => 'Cárdenas',
            'email' => 'jaruny@test.com',
            'password' => Hash::make('123456'),
            'fk_tipo_user' => $this->adminTipo->pk_tipo_user,
        ]);

        // Crea una sesión con el usuario creado
        $this->actingAs($user);

        // Intenta acceder a la ruta de inicio, la cual, está asegurada
        $response = $this->get(route('inicio'));

        // Como el usuario está autenticado, la prueba espera una respuesta 200 del servidor
        $response->assertStatus(200);
    }

    // De igual forma, esta prueba intenta acceder al inicio pero esta vez sin autenticar un usuario,
    // el sistema debe retornar al login cuando el usuario no tiene una sesión activa,
    // por lo tanto, es lo que nuestra prueba espera que pase.

    /** @test */
    public function usuarioNOAutenticadoEnrutamiento(){
        // Sin iniciar sesión, se intenta acceder a la ruta de inicio
        $response = $this->get(route('inicio'));

        // La función espera que un usuario no autenticado sea redireccionado al inicio de sesión (login)
        $response->assertRedirect(route('login'));
    }


}
