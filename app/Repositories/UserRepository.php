<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function findOrFail(int $id)
    {
        return User::findOrFail($id);
    }

    public function delete($user)
    {
        return $user->delete();
    }
}
