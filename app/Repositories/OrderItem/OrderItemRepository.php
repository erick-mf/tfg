<?php

namespace App\Repositories\OrderItem;

use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private OrderItem $orderItem) {}

    public function all()
    {
        try {
            return $this->orderItem->with('order', 'menuItem')->orderBy('id', 'asc')->get();
        } catch (Exception $e) {
            Log::error('Error getting orderItems: '.$e->getMessage());

            throw new RuntimeException('Error al obtener los items del pedido');
        }
    }

    public function paginate(int $perPage = 10)
    {
        try {
            return $this->orderItem->with('order', 'menuItem')->orderBy('id', 'asc')->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error paginating orderItems: '.$e->getMessage());

            throw new RuntimeException('Error al obtener los items del pedido');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->orderItem->with('order', 'menuItem')->find($id);
        } catch (Exception $e) {
            Log::error('Error getting orderItem: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el item del pedido');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->orderItem->create($data);
        } catch (Exception $e) {
            Log::error('Error creating orderItem: '.$e->getMessage());

            throw new RuntimeException('Error al crear el item del pedido');
        }
    }

    public function update(array $data, OrderItem $orderItem)
    {
        try {
            return $orderItem->update($data);
        } catch (Exception $e) {
            Log::error('Error updating orderItem: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar el item del pedido');
        }
    }

    public function delete(OrderItem $orderItem)
    {
        try {
            return $orderItem->delete();
        } catch (Exception $e) {
            Log::error('Error deleting orderItem: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar el item del pedido');
        }
    }
}
