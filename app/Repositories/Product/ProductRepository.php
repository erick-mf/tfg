<?php

namespace App\Repositories\Product;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Product $product) {}

    public function paginate(int $perPage = 10)
    {
        try {
            return $this->product->with('location')->orderBy('id', 'asc')->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Error paginating products: '.$e->getMessage());

            throw new RuntimeException('Error al obtener los productos');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->product->find($id);

        } catch (Exception $e) {
            Log::error('Error finding product: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el producto');
        }
    }

    public function create(array $data)
    {
        try {
            $exists = $this->product->where('name', $data['name'])->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe un producto con ese nombre');
            }

            return $this->product->create($data);
        } catch (Exception $e) {
            Log::error('Error creating product: '.$e->getMessage());

            throw new RuntimeException('Error al crear el producto: '.$e->getMessage());
        }
    }

    public function update(array $data, Product $product)
    {
        try {
            $exists = $this->product->where('name', $data['name'])->where('id', '!=', $product->id)->exists();

            if ($exists) {
                throw new RuntimeException('Ya existe un producto con ese nombre');
            }

            return $product->update($data);
        } catch (Exception $e) {
            Log::error('Error updating product: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar el producto: '.$e->getMessage());
        }
    }

    public function delete(Product $product)
    {
        try {
            $deleted = $product->delete();

            if (! $deleted) {
                throw new RuntimeException('El producto no pudo ser eliminado');
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting product: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar el producto: '.$e->getMessage());
        }
    }
}
