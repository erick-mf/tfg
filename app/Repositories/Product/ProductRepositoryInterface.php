<?php

namespace App\Repositories\Product;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function paginate(int $perPage = 10);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, Product $product);

    public function delete(Product $product);
}
