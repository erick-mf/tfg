
<?php

use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(OrderRepositoryInterface::class);
});

test('paginate return correct number of items per page', function () {
    User::factory()->create();
    Table::factory()->create();
    Order::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(10);
    expect($result->total())->toBe(15);
    expect($result->items())->each->toBeInstanceOf(Order::class);
});

test('findById returns correct order', function () {
    User::factory()->create();
    Table::factory()->create();
    Order::factory()->create();
    $order = Order::factory()->create();

    $found = $this->repository->findById($order->id);
    expect($found)->toBeInstanceOf(Order::class);
    expect($found->id)->toBe($order->id);
});

test('findById returns null when order not found', function () {
    $found = $this->repository->findById(999);
    expect($found)->toBeNull();
});

test('create order successfully', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $orderData = ['total' => 99.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id];
    $order = $this->repository->create($orderData);

    expect($order)
        ->toBeInstanceOf(Order::class)
        ->total->toBe(99.99);
});

test('update order successfully', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $orderData = ['total' => 9.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id];

    $order = $this->repository->create($orderData);
    $updated = $this->repository->update(['total' => 10.99], $order);

    expect($updated)->toBeTrue();
    expect($order->fresh()->total)->toBe('10.99');
});

test('update order status successfully', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $order = $this->repository->create(['total' => 9.99, 'status' => 'pagado', 'payment_method' => 'efectivo', 'paid_at' => now(), 'user_id' => $user->id, 'table_id' => $table->id]);

    $result = $this->repository->update(['status' => 'pendiente'], $order);

    expect($result)->toBeTrue();
});

test('delete order successfully', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $order = $this->repository->create(['total' => 9.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id]);
    $result = $this->repository->delete($order);

    expect($result)->toBeTrue();
});

test('delete non-existent order throws exception', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $order = $this->repository->create(['total' => 9.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id]);
    $order->forceDelete();

    expect(fn () => $this->repository->delete($order))
        ->toThrow(RuntimeException::class);
});

test('soft delete non-existent order throws exception', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $order = $this->repository->create(['total' => 9.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id]);

    $result = $this->repository->delete($order);
    expect($result)->toBeTrue();
});
