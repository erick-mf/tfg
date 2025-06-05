<?php

namespace App\Repositories\Table;

use App\Models\Table;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TableRepository implements TableRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Table $table) {}

    public function all()
    {
        try {
            return $this->table->orderBy('id', 'asc')->get();

        } catch (Exception $e) {
            Log::error('Error getting tables: '.$e->getMessage());

            throw new RuntimeException('Error al obtener las mesas');
        }
    }

    public function paginate(int $perPage = 24)
    {
        try {
            return $this->table->orderBy('id', 'asc')->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Error paginating tables: '.$e->getMessage());

            throw new RuntimeException('Error al obtener las mesas');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->table->find($id);

        } catch (Exception $e) {
            Log::error('Error finding table: '.$e->getMessage());

            throw new RuntimeException('Error al obtener la mesa');
        }
    }

    public function create(array $data)
    {
        try {
            $exists = $this->table->where('name', $data['name'])->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe una mesa con ese nombre');
            }

            return $this->table->create($data);
        } catch (Exception $e) {
            Log::error('Error creating table: '.$e->getMessage());

            throw new RuntimeException('Error al crear la mesa');
        }
    }

    public function update(array $data, Table $table)
    {
        try {
            $exists = $this->table->where('name', $data['name'])->where('id', '!=', $table->id)->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe una mesa con ese nombre');
            }

            return $table->update($data);
        } catch (Exception $e) {
            Log::error('Error updating table: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar la mesa');
        }
    }

    public function delete(Table $table)
    {
        try {
            $deleted = $table->delete();

            if (! $deleted) {
                throw new RuntimeException('La mesa no pudo ser eliminada');
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting table: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar la mesa');
        }
    }

    public function changeStatus(int $id, string $status)
    {
        try {
            $table = $this->findById($id);
            $table->status = $status;
            $table->save();

            return $table;
        } catch (Exception $e) {
            Log::error('Error changing table status: '.$e->getMessage());
            throw new RuntimeException('Error al cambiar el estado de la mesa');
        }
    }
}
