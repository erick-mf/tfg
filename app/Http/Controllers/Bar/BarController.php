<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderItem\OrderItemRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MenuItemRepositoryInterface $menuItemsRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository
    ) {}

    public function index()
    {
        try {
            $orders = $this->orderRepository->getOrderByLocation('barra');
            $menuItems = $this->menuItemsRepository->all();

            return Inertia::render('Bar/Bar', compact('orders', 'menuItems'));

        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOrdersData()
    {
        try {
            $activeOrders = $this->orderRepository->getOrderByLocation('barra');
            $readyOrders = $this->orderRepository->getReadyOrdersByLocation('barra');

            return response()->json(['activeOrders' => $activeOrders, 'readyOrders' => $readyOrders]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        try {
            $this->menuItemsRepository->changeAvailability($menuItem);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'La disponibilidad del producto ha sido actualizada.']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'string', 'in:en preparacion,listo,cancelado'],
            ]);

            $this->orderItemRepository->update($validated, $orderItem);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'El estado del ítem ha sido actualizado.']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
