<?php

namespace App\Repositories\Location;

use App\Models\Location;

interface LocationRepositoryInterface
{
    public function paginate(int $perPage = 15);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, Location $location);

    public function delete(Location $location);
}
