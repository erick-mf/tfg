<?php

namespace App\Repositories\User;

use App\Models\User;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $user) {}

    public function all()
    {
        try {
            return $this->user->whereNot('role', 'admin')->whereNot('role', 'encargado')->orderBy('id', 'asc')->get();
        } catch (Exception $e) {
            throw new Exception('Error getting users');
        }
    }

    public function paginate(int $perPage = 10)
    {
        return $this->user->whereNot('role', 'admin')->orderBy('id', 'asc')->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->user->find($id);
    }

    public function findByPassword($password)
    {
        return $this->user->where('password', $password)->first();
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
