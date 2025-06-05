<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Table\TableRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TableRepositoryInterface $tableRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $orders = $this->orderRepository->paginate();
            $users = $this->userRepository->all();
            $tables = $this->tableRepository->all();

            if ($orders->isEmpty() && $orders->currentPage() > 1) {
                $targetPage = $orders->lastPage() > 0 ? $orders->lastPage() : 1;
                if ($targetPage == $orders->currentPage() && $targetPage > 1) {
                    $targetPage--;
                }
                $targetPage = max(1, $targetPage);

                return redirect()->route('admin.orders.index', ['page' => $targetPage]);
            }

            return Inertia::render('Admin/Order', compact('orders', 'users', 'tables'));
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order)
    {
        try {
            $validated = $request->validated();
            $this->orderRepository->update($validated, $order);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Pedido actualizado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $this->orderRepository->delete($order);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Pedido eliminado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
