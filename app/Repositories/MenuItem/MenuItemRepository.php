<?php

namespace App\Repositories\MenuItem;

use App\Models\MenuItem;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private MenuItem $menuItem) {}

    public function all()
    {
        try {
            return $this->menuItem->with('category')->get();

        } catch (Exception $e) {
            Log::error('Error getting menu items: '.$e->getMessage());

            throw new RuntimeException('Error al obtener los menús');
        }
    }

    public function paginate(int $perPage = 7)
    {
        try {
            return $this->menuItem->with('category')->orderBy('id', 'asc')->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Error paginating menu items: '.$e->getMessage());

            throw new RuntimeException('Error al paginar los menús');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->menuItem->with('category')->find($id);

        } catch (Exception $e) {
            Log::error('Error finding menu item: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el menú');
        }
    }

    public function create(array $data)
    {
        try {
            $exists = $this->menuItem->where('name', $data['name'])->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe un menú con ese nombre');
            }

            return $this->menuItem->create($data);
        } catch (Exception $e) {
            Log::error('Error creating menu item: '.$e->getMessage());

            throw new RuntimeException('Error al crear el menú: '.$e->getMessage());
        }
    }

    public function update(array $data, MenuItem $menuItem)
    {
        try {
            $exists = $this->menuItem->where('name', $data['name'])->where('id', '!=', $menuItem->id)->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe un menú con ese nombre');
            }

            return $menuItem->update($data);
        } catch (Exception $e) {
            Log::error('Error updating menu item: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar el menú: '.$e->getMessage());
        }
    }

    public function delete(MenuItem $menuItem)
    {
        try {
            $deleted = $menuItem->delete();

            if (! $deleted) {
                throw new RuntimeException('El menú no pudo ser eliminado');
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting menu item: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar el menú: '.$e->getMessage());
        }
    }
}
