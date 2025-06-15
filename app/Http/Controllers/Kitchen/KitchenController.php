<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderItem\OrderItemRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KitchenController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MenuItemRepositoryInterface $menuItemsRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository
    ) {}

    public function index()
    {
        try {
            $orders = $this->orderRepository->getOrderByLocation('cocina');
            $menuItems = $this->menuItemsRepository->all();

            return Inertia::render('Kitchen/Kitchen', compact('orders', 'menuItems'));

        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOrdersData()
    {
        try {
            $activeOrders = $this->orderRepository->getOrderByLocation('cocina');
            $readyOrders = $this->orderRepository->getReadyOrdersByLocation('cocina');

            return response()->json(['activeOrders' => $activeOrders, 'readyOrders' => $readyOrders]);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        try {
            $menuItem = $this->menuItemsRepository->changeAvailability($menuItem);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'El menú ha sido actualizado']);
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

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'El item del pedido ha sido actualizado']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
