<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Table\TableRepositoryInterface;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Order $order, private TableRepositoryInterface $table) {}

    public function all()
    {
        try {
            return $this->order->all();
        } catch (Exception $e) {
            Log::error('Error getting orders: '.$e->getMessage());

            throw new RuntimeException('Error al obtener los pedidos');
        }
    }

    public function paginate(int $perPage = 10)
    {
        try {
            return $this->order->with('user', 'assignedTable', 'orderItems.menuItem')->orderBy('id', 'asc')->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Error paginating orders: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el pedido');
        }
    }

    public function findById(int $id)
    {
        try {
            return $this->order->find($id);
        } catch (Exception $e) {
            Log::error('Error getting order: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el pedido');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->order->create($data);
        } catch (Exception $e) {
            Log::error('Error creating order: '.$e->getMessage());

            throw new RuntimeException('Error al crear el pedido');
        }
    }

    public function update(array $data, Order $order)
    {
        try {
            return DB::transaction(function () use ($data, $order) {
                $itemsData = Arr::pull($data, 'items', []);
                $originalTableId = $order->table_id;
                $currentStatus = $order->status;

                if (isset($data['table_id']) && $data['table_id'] != $originalTableId) {
                    $newTable = $this->table->findById($data['table_id']);
                    if (! $newTable) {
                        throw new RuntimeException('La mesa seleccionada no existe');
                    }
                    if ($newTable->status !== 'disponible') {
                        throw new RuntimeException('La mesa seleccionada no está disponible');
                    }

                    if ($originalTableId) {
                        $this->table->changeStatus($originalTableId, 'disponible');
                    }
                    $this->table->changeStatus($data['table_id'], 'ocupada');
                }

                if (isset($data['status'])) {
                    $newStatus = $data['status'];

                    if ($newStatus === 'pagado') {
                        if ($order->table_id && $currentStatus !== 'pagado') {
                            $this->table->changeStatus($order->table_id, 'disponible');
                        }
                        if ($currentStatus !== 'pagado') {
                            $data['paid_at'] = now();
                        }
                    } else {
                        $data['paid_at'] = null;
                        $data['payment_method'] = null;

                        if ($currentStatus === 'pagado' &&
                            $order->table_id &&
                            (! isset($data['table_id']) || $data['table_id'] == $originalTableId)
                        ) {
                            $this->table->changeStatus($order->table_id, 'ocupada');
                        }
                    }
                }

                $order->update($data);

                $incomingItemIds = collect($itemsData)->pluck('id')->filter()->all();

                OrderItem::where('order_id', $order->id)
                    ->whereNotIn('id', $incomingItemIds)
                    ->delete();

                foreach ($itemsData as $itemData) {
                    $fillableData = Arr::only($itemData, (new OrderItem)->getFillable());

                    OrderItem::updateOrCreate(
                        ['id' => $itemData['id'] ?? null, 'order_id' => $order->id],
                        $fillableData
                    );
                }

                $order->total = $order->fresh()->load('orderItems')->orderItems->sum('subtotal');
                $order->save();

                return true;
            });
        } catch (Exception $e) {
            Log::error('Error updating order and its items: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar el pedido: '.$e->getMessage(), 0, $e);
        }
    }

    public function delete(Order $order)
    {
        try {
            return DB::transaction(function () use ($order) {
                // Liberar la mesa si está asignada
                if ($order->table_id) {
                    $this->table->changeStatus($order->table_id, 'disponible');
                }

                $deleted = $order->delete();
                if (! $deleted) {
                    throw new RuntimeException('Error al eliminar el pedido');
                }

                return $deleted;
            });
        } catch (Exception $e) {
            Log::error('Error deleting order: '.$e->getMessage());

            throw new RuntimeException('Error al eliminar el pedido');
        }
    }
}
