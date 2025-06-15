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
        return DB::transaction(function () use ($data) {
            $itemsData = Arr::pull($data, 'items', []);
            $tableId = $data['table_id'] ?? null;

            $order = $this->order->create($data);

            if ($tableId) {
                $this->table->changeStatus($tableId, 'ocupada');
            }

            $total = 0;

            foreach ($itemsData as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_price'];
                $itemData['subtotal'] = $subtotal;
                $item = $order->orderItems()->create($itemData);

                $total += $subtotal;
            }

            $order->total = $total;
            $order->save();

            return $order;
        });
    }

    public function update(array $data, Order $order)
    {
        try {
            return DB::transaction(function () use ($data, $order) {
                $itemsData = Arr::pull($data, 'items', []);

                if (isset($data['status']) && $data['status'] === 'pagado') {
                    if ($order->table_id && $order->status !== 'pagado') {
                        $this->table->changeStatus($order->table_id, 'disponible');
                    }
                    if ($order->status !== 'pagado') {
                        $data['paid_at'] = now();
                    }
                }
                $order->update(Arr::except($data, ['items']));

                $incomingItemIds = collect($itemsData)->pluck('id')->filter()->all();
                OrderItem::where('order_id', $order->id)
                    ->whereNotIn('id', $incomingItemIds)
                    ->delete();

                foreach ($itemsData as $itemData) {
                    $itemData['subtotal'] = $itemData['quantity'] * $itemData['unit_price'];

                    OrderItem::updateOrCreate(
                        [
                            'id' => $itemData['id'] ?? null,
                            'order_id' => $order->id,
                        ],
                        $itemData
                    );
                }

                $order->total = $order->fresh()->orderItems()->sum('subtotal');
                $order->save();

                return $order;
            });
        } catch (Exception $e) {
            Log::error('Error updating order and its items: '.$e->getMessage());

            throw new RuntimeException('Error al actualizar el pedido');
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

    public function getOrderByLocation(string $location)
    {
        return $this->order
            ->whereIn('status', ['enviado', 'en preparacion'])
            ->whereHas('orderItems', function ($query) use ($location) {
                $query
                    ->whereIn('status', ['pendiente', 'enviado', 'en preparacion'])
                    ->whereHas('menuItem', function ($subQuery) use ($location) {
                        $subQuery->where('location', $location);
                    });
            })
            ->with([
                'user:id,name',
                'assignedTable:id,name',

                'orderItems' => function ($query) use ($location) {
                    $query->whereHas('menuItem', function ($subQuery) use ($location) {
                        $subQuery->where('location', $location);
                    });
                },
                'orderItems.menuItem:id,name,location',
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getReadyOrdersByLocation(string $location)
    {
        return $this->order
            ->whereIn('status', ['pendiente', 'en preparacion'])
            ->whereHas('orderItems', function ($query) use ($location) {
                $query->whereHas('menuItem', fn ($q) => $q->where('location', $location));
            })
            ->whereDoesntHave('orderItems', function ($query) use ($location) {
                $query->whereIn('status', ['pendiente', 'enviado', 'en preparacion'])
                    ->whereHas('menuItem', fn ($q) => $q->where('location', $location));
            })
            ->with([
                'user:id,name',
                'assignedTable:id,name',
                'orderItems' => fn ($q) => $q->whereHas('menuItem', fn ($sq) => $sq->where('location', 'cocina')),
                'orderItems.menuItem:id,name,location',
            ])
            ->orderBy('updated_at', 'desc')
            ->limit(16)
            ->get();
    }
}
