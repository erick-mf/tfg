<?php

namespace App\Repositories\MenuItem;

use App\Models\MenuItem;

interface MenuItemRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 7);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, MenuItem $menuItem);

    public function delete(MenuItem $menuItem);

    public function getMenuItemsAvailable();
}
