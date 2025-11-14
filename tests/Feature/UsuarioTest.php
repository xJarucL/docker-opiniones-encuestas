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

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TipoUsuarioSeeder::class);

        $this->adminTipo = Tipo_usuario::where('nombre', 'Administrador')->first();
        $this->alumnoTipo = Tipo_usuario::where('nombre', 'Común')->first();
    }

    /** @test */
    public function login()
    {
        $user = User::create([
            'username' => 'testuser',
            'nombres' => 'Test',
            'ap_paterno' => 'Usuario',
            'ap_materno' => 'Prueba',
            'email' => 'test@correo.com',
            'password' => Hash::make('test'),
            'fk_tipo_user' => $this->adminTipo->pk_tipo_user,
        ]);

        $response = $this->post('/iniciando_sesion', [
            'email' => 'test@correo.com',
            'password' => 'test',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => '¡Inicio de sesión exitoso!',
                     'class' => 'success',
                     'ruta' => route('inicio')
                 ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function usuarioAutenticadoEnrutamiento(){
        $user = User::create([
            'username' => 'jarucl',
            'nombres' => 'Jaruny',
            'ap_paterno' => 'Cárdenas',
            'email' => 'jaruny@test.com',
            'password' => Hash::make('123456'),
            'fk_tipo_user' => $this->adminTipo->pk_tipo_user,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
    }

    /** @test */
    public function usuarioNOAutenticadoEnrutamiento(){
        $response = $this->get(route('inicio'));

        $response->assertRedirect(route('login'));
    }


}
