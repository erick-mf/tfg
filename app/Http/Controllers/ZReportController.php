<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Repositories\ZReport\ZReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ZReportController extends Controller
{
    public function __construct(private readonly ZReportRepositoryInterface $zReportRepository) {}

    public function index(): InertiaResponse
    {
        $lastCierre = $this->zReportRepository->findLast();
        $startDate = $lastCierre ? $lastCierre->end_date : Carbon::today()->startOfDay();

        $summary = $this->zReportRepository->getSummaryForNewReport(Carbon::parse($startDate));
        $summary['startDate'] = Carbon::parse($startDate)->format('d-m-Y H:i');

        $activeOrdersCount = Order::whereIn('status', ['pendiente', 'en preparacion'])->count();
        $occupiedTablesCount = Table::where('status', 'ocupada')->count();

        $validationData = [
            'canClose' => $activeOrdersCount === 0 && $occupiedTablesCount === 0,
            'messages' => $this->getValidationMessages($activeOrdersCount, $occupiedTablesCount),
        ];
        $historyData = $this->zReportRepository->getPaginateHistory();

        return Inertia::render('Report/ZReport', [
            'formData' => [
                'summary' => $summary,
                'validation' => $validationData],
            'historyData' => $historyData,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'counted_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'counted_cash.required' => 'El monto contado es requerido.',
            'counted_cash.numeric' => 'El monto contado debe ser un valor numérico.',
            'counted_cash.min' => 'El monto contado debe ser mayor o igual a cero.',
            'notes.max' => 'Las notas deben tener un máximo de 1000 caracteres.',
        ]);

        if (Order::whereIn('status', ['pendiente', 'en preparacion'])->exists() || Table::where('status', 'ocupada')->exists()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Error: Todavía hay mesas o pedidos activos. No se puede cerrar la caja.',
            ]);
        }

        $lastCierre = $this->zReportRepository->findLast();
        $startDate = $lastCierre ? $lastCierre->end_date : Carbon::today()->startOfDay();
        $summary = $this->zReportRepository->getSummaryForNewReport(Carbon::parse($startDate));

        $this->zReportRepository->create([
            'user_id' => Auth::id(),
            'start_date' => $startDate,
            'end_date' => now(),
            'total_revenue' => $summary['totalRevenue'],
            'expected_cash' => $summary['expectedCash'],
            'total_card' => $summary['totalCard'],
            'total_orders' => $summary['totalOrders'],
            'counted_cash' => $validated['counted_cash'],
            'cash_difference' => $validated['counted_cash'] - $summary['expectedCash'],
            'notes' => $validated['notes'],
        ]);

        Table::whereIn('status', ['ocupada', 'reservada'])->update(['status' => 'disponible']);

        return redirect()->back()
            ->with('toast', ['type' => 'success', 'message' => 'Caja cerrada exitosamente.']);
    }

    private function getValidationMessages(int $activeOrdersCount, int $occupiedTablesCount): array
    {
        $messages = [];
        if ($activeOrdersCount > 0) {
            $messages[] = "Hay {$activeOrdersCount} pedido(s) activos (en preparación o pendientes).";
        }
        if ($occupiedTablesCount > 0) {
            $messages[] = "Hay {$occupiedTablesCount} mesa(s) marcadas como 'Ocupada'. Deben ser cobradas antes de cerrar.";
        }

        return $messages;
    }
}
