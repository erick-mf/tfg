<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\Table\TableRepositoryInterface;
use Exception;
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
            return $this->order->with('user', 'assignedTable')->orderBy('id', 'asc')->paginate($perPage);

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
                // guardar la informacion anterior
                $originalTableId = $order->table_id;
                $currentStatus = $order->status;

                // cambio de mesa
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
                    } else { // Si el nuevo estado NO es 'pagado'
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

                return $order->update($data);
            });
        } catch (Exception $e) {
            Log::error('Error updating order: '.$e->getMessage());

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
}
