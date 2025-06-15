<?php

namespace App\Repositories\Order;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 10);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, Order $order);

    public function delete(Order $order);

    public function getOrderByLocation(string $location);

    public function getReadyOrdersByLocation(string $location);
}
