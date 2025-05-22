<?php

namespace App\Repositories\User;

use App\Models\User;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $user) {}

    public function paginate(int $perPage = 10)
    {
        return $this->user->paginate($perPage)->getCollection();
    }

    public function findById($id)
    {
        return $this->user->find($id);
    }

    public function create(array $data)
    {
        return $this->user->create($data);
    }

    public function update(array $data, User $user)
    {
        $updated = $user->update($data);
        if (! $updated) {
            throw new Exception('Error updating user');
        }

        return $user;
    }

    public function delete(User $user)
    {
        $deleted = $user->delete();
        if (! $deleted) {
            throw new Exception('Error deleting user');
        }

        return true;
    }
}
