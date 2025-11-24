<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class UsuarioTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }


    // Prueba que un usuario pueda registrarse correctamente cuando todos los datos son válidos.
    public function RegistrarUsuarioExitosamente(){
        // Creamos un mock del repositorio
        $mockRepo = Mockery::mock(UserRepositoryInterface::class);

        // Indicamos que el correo NO existe previamente
        $mockRepo->shouldReceive('emailExists')
            ->with('correo@test.com')
            ->andReturn(false);

        // Simulamos la respuesta de creación exitosa del usuario
        $mockUserResult = (object) [
            'id' => 1,
            'username' => 'demo',
            'nombres' => 'Demo',
            'ap_paterno' => 'Test',
            'email' => 'correo@test.com'
        ];

        // Mock del método create del repositorio
        $mockRepo->shouldReceive('create')
            ->andReturn($mockUserResult);

        // Instanciamos el servicio con el mock
        $service = new UserService($mockRepo);

        // Ejecutamos la función de guardar usuario
        $result = $service->saveUser([
            'username' => 'demo',
            'nombres' => 'Demo',
            'ap_paterno' => 'Test',
            'email' => 'correo@test.com',
            'password' => '123456'
        ]);

        // Verificamos que regresó el ID esperado
        $this->assertEquals(1, $result->id);
    }


    // Prueba que no permita registrar un usuario si el correo ya existe.
    public function test_no_permite_correo_duplicado(){
        // Creamos mock del repositorio
        $repo = Mockery::mock(UserRepositoryInterface::class);

        // Indicamos que el correo ya está registrado
        $repo->shouldReceive('emailExists')
             ->once()
             ->andReturn(true);

        // Instanciamos servicio
        $service = new UserService($repo);

        // Esperamos una excepción por correo duplicado
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El correo ya está registrado.');

        // Intentamos registrar un usuario con correo repetido
        $service->saveUser([
            'username' => 'juan',
            'nombres' => 'Juan',
            'ap_paterno' => 'Lopez',
            'email' => 'duplicado@gmail.com',
        ]);
    }

    /**
     * Prueba que no se permita eliminar al usuario administrador.
     */
    public function test_no_puede_eliminar_admin(){
        // Mock del repositorio
        $repo = Mockery::mock(UserRepositoryInterface::class);

        // Simulamos que se encuentra un usuario administrador (fk_tipo_user = 1)
        $repo->shouldReceive('findOrFail')
             ->once()
             ->with(1)
             ->andReturn((object)[
                 'fk_tipo_user' => 1
             ]);

        // Instanciamos el servicio
        $service = new UserService($repo);

        // Esperamos excepción por intentar eliminar al admin
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No puedes eliminar al administrador.');

        // Intento de eliminación
        $service->deleteUser(1);
    }

    // Prueba que un usuario normal pueda eliminarse sin problemas.
    public function test_eliminar_usuario_normal(){
        // Mock del repositorio
        $repo = Mockery::mock(UserRepositoryInterface::class);

        // Creamos un usuario falso con rol normal (fk_tipo_user = 2)
        $fakeUser = Mockery::mock();
        $fakeUser->fk_tipo_user = 2;

        // Simulamos que el usuario fue encontrado
        $repo->shouldReceive('findOrFail')
             ->once()
             ->with(5)
             ->andReturn($fakeUser);

        // Simulamos eliminación exitosa
        $repo->shouldReceive('delete')
             ->once()
             ->with($fakeUser)
             ->andReturn(true);

        // Instanciamos servicio
        $service = new UserService($repo);

        // Ejecutamos eliminación
        $result = $service->deleteUser(5);

        // Verificamos que regrese true
        $this->assertTrue($result);
    }
}
