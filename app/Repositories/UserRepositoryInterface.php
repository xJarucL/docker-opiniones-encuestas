<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function emailExists(string $email): bool;
    public function create(array $data);
    public function findOrFail(int $id);
    public function delete($user);
}
