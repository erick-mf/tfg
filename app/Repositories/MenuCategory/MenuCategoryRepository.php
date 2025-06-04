<?php

namespace App\Repositories\MenuCategory;

use App\Models\MenuCategory;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MenuCategoryRepository implements MenuCategoryRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private MenuCategory $menuCategory) {}

    public function all()
    {
        try {
            return $this->menuCategory->all();

        } catch (Exception $e) {
            Log::error('Error getting menu categories: '.$e->getMessage());

            throw new RuntimeException('Error al obtener las categorías');
        }
    }

    public function paginate(int $perPage = 24)
    {
        try {
            return $this->menuCategory->orderBy('id', 'asc')->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Error paginating menu categories: '.$e->getMessage());

            throw new RuntimeException('Error al obtener las categorías');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->menuCategory->find($id);

        } catch (Exception $e) {
            Log::error('Error finding menu category: '.$e->getMessage());

            throw new RuntimeException('Error al obtener la categoría');
        }
    }

    public function create(array $data)
    {
        try {
            $exists = $this->menuCategory->where('name', $data['name'])->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe una categoría con ese nombre');
            }

            return $this->menuCategory->create($data);
        } catch (Exception $e) {
            Log::error('Error creating menu category: '.$e->getMessage());

            throw new RuntimeException('Error al crear la categoría: '.$e->getMessage());
        }
    }

    public function update(array $data, MenuCategory $menuCategory)
    {
        try {
            $exists = $this->menuCategory->where('name', $data['name'])->where('id', '!=', $menuCategory->id)->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe una categoría con ese nombre');
            }

            return $menuCategory->update($data);
        } catch (Exception $e) {
            Log::error('Error updating menu category: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar la categoría: '.$e->getMessage());
        }
    }

    public function delete(MenuCategory $menuCategory)
    {
        try {
            $deleted = $menuCategory->delete();

            if (! $deleted) {
                throw new RuntimeException('La categoría no pudo ser eliminada');
            }

            return $deleted;

        } catch (Exception $e) {
            Log::error('Error deleting menu category: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar la categoría: '.$e->getMessage());
        }
    }
}
