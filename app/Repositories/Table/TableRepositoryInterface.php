<?php

namespace App\Repositories\Table;

use App\Models\Table;

interface TableRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 24);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, Table $table);

    public function delete(Table $table);
}
