<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Comentario;
use App\Models\Tipo_usuario;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComentarioTest extends TestCase
{
    use RefreshDatabase;

    protected $adminTipo;
    protected $comunTipo;

    protected function setUp(): void
    {
        parent::setUp();

        // Cargar los tipos de usuario para asignarlos en las pruebas
        $this->seed(TipoUsuarioSeeder::class);

        $this->adminTipo = Tipo_usuario::where('nombre', 'Administrador')->first();
        $this->comunTipo = Tipo_usuario::where('nombre', 'Común')->first();
    }


    
    /** @test */
     //Esta prueba verifica que un usuario autenticado pueda publicar un comentario
     //en el perfil de otro usuario. 
    // Valida que el usuario esté autenticado
     //Envía la información del comentario al controlador
     //Confirma que el sistema redirige correctamente
     //Verifica que se muestre el mensaje de éxito
     //Revisa que el comentario haya sido guardado en la base de datos
     

    public function usuario_puede_crear_comentario_en_otro_perfil()
    {
        // Usuario que hará el comentario
        $autor = User::create([
            'username'      => 'autor',
            'nombres'       => 'Autor',
            'ap_paterno'    => 'Prueba',
            'ap_materno'    => 'Test',
            'email'         => 'autor@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->adminTipo->pk_tipo_user,
        ]);

        // Usuario dueño del perfil donde se dejará el comentario
        $perfil = User::create([
            'username'      => 'perfil',
            'nombres'       => 'Perfil',
            'ap_paterno'    => 'Usuario',
            'ap_materno'    => 'Destino',
            'email'         => 'perfil@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->comunTipo->pk_tipo_user,
        ]);

        // Autenticar al autor
        $this->actingAs($autor);

        // Enviar petición al endpoint real
        $response = $this->post(route('comentarios.store', $perfil->pk_usuario), [
            'contenido' => 'Este es un comentario de prueba',
            'anonimo'   => true,
        ]);

        // El controlador debe redirigir hacia atrás
        $response->assertRedirect();

        // Debe traer mensaje de éxito
        $response->assertSessionHas('success', 'Comentario publicado correctamente.');

        // Validación en la base de datos
        $this->assertDatabaseHas('comentarios', [
            'fk_autor'        => $autor->pk_usuario,
            'fk_perfil_user'  => $perfil->pk_usuario,
            'contenido'       => 'Este es un comentario de prueba',
            'anonimo'         => 1,
            'estatus'         => 'visible',
        ]);
    }



    /** @test */
     //Esta prueba valida que un usuario autenticado pueda publicar una respuesta
     //a un comentario existente. Esto asegura que la lógica de "responder"
     //funciona adecuadamente:
     
     //Se valida que el usuario esté autenticado
     //Se envía la información de la respuesta al endpoint correcto
     //El sistema debe redirigir correctamente
     //Se valida el mensaje de éxito
     //Se confirma que la respuesta quedó ligada al comentario original
     //mediante 'fk_coment_respuesta'
    
    public function usuario_puede_responder_comentario()
    {
        // Usuario autor del comentario y la respuesta
        $autor = User::create([
            'username'      => 'autor2',
            'nombres'       => 'Autor',
            'ap_paterno'    => 'Responder',
            'ap_materno'    => 'Test',
            'email'         => 'autor2@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->adminTipo->pk_tipo_user,
        ]);

        // Perfil donde se dejó el comentario original
        $perfil = User::create([
            'username'      => 'perfil2',
            'nombres'       => 'Perfil',
            'ap_paterno'    => 'Usuario',
            'ap_materno'    => 'Destino',
            'email'         => 'perfil2@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->comunTipo->pk_tipo_user,
        ]);

        // Comentario original que se va a responder
        $comentarioOriginal = Comentario::create([
            'fk_autor'        => $autor->pk_usuario,
            'fk_perfil_user'  => $perfil->pk_usuario,
            'contenido'       => 'Comentario original',
            'anonimo'         => false,
            'estatus'         => 'visible',
        ]);

        $this->actingAs($autor);

        // Enviar respuesta
        $response = $this->post(
            route('comentarios.reply', $comentarioOriginal->pk_comentario),
            ['contenido' => 'Respuesta de prueba']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Respuesta publicada correctamente.');

        // Validar que la respuesta quedó ligada al comentario original
        $this->assertDatabaseHas('comentarios', [
            'fk_coment_respuesta' => $comentarioOriginal->pk_comentario,
            'contenido'           => 'Respuesta de prueba',
            'estatus'             => 'visible',
        ]);
    }

    
    /** @test */
     //Esta prueba verifica que el autor de un comentario pueda eliminarlo.
     //La función destroy cambia el estatus del comentario y ejecuta delete():
     
    // Se autentica al autor del comentario
     //Se envía una petición DELETE a la ruta correspondiente
     //El sistema debe redirigir con mensaje de éxito
     //Se valida que el registro cambió el estatus a "eliminado"
     //Si el modelo usa SoftDeletes, también queda marcado como eliminado
     
     //Esta prueba confirma que la política de autorización y la lógica
     //del controlador funcionan correctamente.
     
    public function autor_puede_eliminar_su_comentario()
    {
        $autor = User::create([
            'username'      => 'autor3',
            'nombres'       => 'Autor',
            'ap_paterno'    => 'Borrar',
            'ap_materno'    => 'Test',
            'email'         => 'autor3@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->adminTipo->pk_tipo_user,
        ]);

        $perfil = User::create([
            'username'      => 'perfil3',
            'nombres'       => 'Perfil',
            'ap_paterno'    => 'Usuario',
            'ap_materno'    => 'Destino',
            'email'         => 'perfil3@test.com',
            'password'      => Hash::make('123456'),
            'fk_tipo_user'  => $this->comunTipo->pk_tipo_user,
        ]);

        // Comentario que se va a eliminar
        $comentario = Comentario::create([
            'fk_autor'        => $autor->pk_usuario,
            'fk_perfil_user'  => $perfil->pk_usuario,
            'contenido'       => 'Comentario a eliminar',
            'anonimo'         => false,
            'estatus'         => 'visible',
        ]);

        $this->actingAs($autor);

        // Ejecutar eliminación
        $response = $this->delete(
            route('comentarios.destroy', $comentario->pk_comentario)
        );

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Comentario eliminado.');

        // Validar que cambió el estatus
        $this->assertDatabaseHas('comentarios', [
            'pk_comentario' => $comentario->pk_comentario,
            'estatus'       => 'eliminado',
        ]);
    }
}
