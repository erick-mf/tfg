<?php

namespace App\Providers;

use App\Repositories\Location\LocationRepository;
use App\Repositories\Location\LocationRepositoryInterface;
use App\Repositories\MenuCategory\MenuCategoryRepository;
use App\Repositories\MenuCategory\MenuCategoryRepositoryInterface;
use App\Repositories\MenuItem\MenuItemRepository;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Table\TableRepository;
use App\Repositories\Table\TableRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(MenuCategoryRepositoryInterface::class, MenuCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(MenuItemRepositoryInterface::class, MenuItemRepository::class);
        $this->app->bind(TableRepositoryInterface::class, TableRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
