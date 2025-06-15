<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\WaiterOrderRequest;
use App\Models\Order;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Table\TableRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WaiterController extends Controller
{
    public function __construct(
        private readonly TableRepositoryInterface $tableRepository,
        private readonly MenuItemRepositoryInterface $menuItemRepository,
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function index()
    {
        $waiterId = Auth::id();

        $allTables = $this->tableRepository->getAllWithActiveOrders();

        $tablesForView = $allTables->map(function ($table) use ($waiterId) {

            $isMine = false;
            $isEditable = false;

            if ($table->status === 'disponible') {
                $isEditable = true;
            } elseif ($table->activeOrder) {
                if ($table->activeOrder->user_id === $waiterId) {
                    $isMine = true;
                    $isEditable = true;
                }
            }

            $tableData = $table->toArray();

            $tableData['is_mine'] = $isMine;
            $tableData['is_editable'] = $isEditable;

            return $tableData;
        });

        $menuItems = $this->menuItemRepository->getMenuItemsAvailable();

        return Inertia::render('Waiter/Waiter', [
            'tables' => $tablesForView,
            'menuItems' => $menuItems,
        ]);
    }

    public function storeOrder(WaiterOrderRequest $request)
    {
        $this->orderRepository->create($request->validated());

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Pedido creado.']);
    }

    public function updateOrder(WaiterOrderRequest $request, Order $order)
    {
        $this->orderRepository->update($request->validated(), $order);

        $message = 'Pedido actualizado.';
        if ($request->input('status') === 'pagado') {
            $message = 'El pedido ha sido marcado como pagado.';
        }

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => $message]);
    }
}
