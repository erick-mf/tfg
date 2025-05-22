<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private readonly UserRepositoryInterface $repository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->repository->paginate();

        return Inertia::render('Admin/Users/Index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->repository->create($validated);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'success', 'message' => 'Usuario creado correctamente']);
        } catch (Exception $e) {
            Log::error('Error creating user: '.$e);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'error', 'message' => 'Ha ocurrido un error al crear el usuario']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();
            $this->repository->update($validated, $user);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'success', 'message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            Log::error('Error updating user: '.$e);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'error', 'message' => 'Ha ocurrido un error al actualizar el usuario']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $this->repository->delete($user);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'success', 'message' => 'Usuario eliminado correctamente']);
        } catch (Exception $e) {
            Log::error('Error deleting user: '.$e);

            return redirect()->route('admin.users.index')->with('toast', ['type' => 'error', 'message' => 'Ha ocurrido un error al eliminar el usuario']);
        }
    }
}
