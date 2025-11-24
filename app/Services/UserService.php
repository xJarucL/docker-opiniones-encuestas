<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserService
{
    protected $repo;

    public function __construct(UserRepositoryInterface $repo){
        $this->repo = $repo;
    }

    /**
     * @param array $data
     * @param \App\Models\User|null
     * @param \Illuminate\Http\UploadedFile|null
     * @return \App\Models\User
     */
    public function saveModel(array $data, ?User $usuario = null, $file = null): User{
        $isEdit = (bool) $usuario;

        if (! $isEdit && $this->repo->emailExists($data['email'])) {
            throw new \Exception('El correo ya está registrado.');
        }

        $usuario = $usuario ?: new User();

        $usuario->username   = $data['username'] ?? $usuario->username;
        $usuario->nombres    = $data['nombres'] ?? $usuario->nombres;
        $usuario->ap_paterno = $data['ap_paterno'] ?? $usuario->ap_paterno;
        $usuario->ap_materno = $data['ap_materno'] ?? $usuario->ap_materno;
        $usuario->email      = $data['email'] ?? $usuario->email;

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        if ($file) {
            if ($isEdit && $usuario->img_user && Storage::disk('public')->exists($usuario->img_user)) {
                Storage::disk('public')->delete($usuario->img_user);
            }

            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('usuarios', $filename, 'public');
            $usuario->img_user = $path;
        }

        if (! $isEdit) {
            $usuario->remember_token = Str::random(10);
            if (empty($data['fk_tipo_user'])) {
                $usuario->fk_tipo_user = 2;
            } else {
                $usuario->fk_tipo_user = $data['fk_tipo_user'];
            }
        }

        $usuario->save();

        return $usuario;
    }

    public function deleteUser(int $id): bool{
        $usuario = $this->repo->findOrFail($id);

        if ($usuario->fk_tipo_user == 1) {
            throw new \Exception('No puedes eliminar al administrador.');
        }

        return $this->repo->delete($usuario);
    }

    public function saveUser(array $data){
        if ($this->repo->emailExists($data['email'])) {
            throw new \Exception('El correo ya está registrado.');
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        return $this->repo->create($data);
    }

}
