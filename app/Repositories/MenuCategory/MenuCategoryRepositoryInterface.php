<?php

namespace App\Repositories\MenuCategory;

use App\Models\MenuCategory;

interface MenuCategoryRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 10);

    public function findById(int $id);

    public function create(array $data);

    public function update(array $data, MenuCategory $menuCategory);

    public function delete(MenuCategory $menuCategory);
}
