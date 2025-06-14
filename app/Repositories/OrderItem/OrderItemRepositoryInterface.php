<?php

namespace App\Repositories\OrderItem;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 10);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, OrderItem $orderItem);

    public function delete(OrderItem $orderItem);
}
