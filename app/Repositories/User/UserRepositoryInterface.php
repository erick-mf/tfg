<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 10);

    public function findById($id);

    public function findByPassword($password);

    public function create(array $data);

    public function update(array $data, User $user);

    public function delete(User $user);
}
