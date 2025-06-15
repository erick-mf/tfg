<?php

namespace App\Repositories\ZReport;

use App\Models\Order;
use App\Models\ZReport;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ZReportRepository implements ZReportRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ZReport $zReport, private Order $order) {}

    public function findById(int $id)
    {
        try {
            return $this->zReport->with('user')->find($id);

        } catch (Exception $e) {
            Log::error('Error getting zReport: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el zReport');
        }
    }

    public function findLast()
    {
        try {
            return $this->zReport->with('user')->latest('end_date')->first();
        } catch (Exception $e) {
            Log::error('Error getting zReport: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el zReport');
        }
    }

    public function getPaginateHistory(int $perPage = 10)
    {
        try {
            return $this->zReport->with('user')->latest('end_date')->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error getting zReport: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el zReport');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->zReport->create($data);
        } catch (Exception $e) {
            Log::error('Error creating zReport: '.$e->getMessage());

            throw new RuntimeException('Error al crear el zReport');
        }
    }

    public function getSummaryForNewReport($startDate)
    {
        try {
            $ordersInPeriod = $this->order->where('status', 'pagado')->where('paid_at', '>=', $startDate);
            $expectedCash = (clone $ordersInPeriod)->where('payment_method', 'efectivo')->sum('total');
            $totalCard = (clone $ordersInPeriod)->where('payment_method', 'tarjeta')->sum('total');

            return [
                'expectedCash' => $expectedCash,
                'totalCard' => $totalCard,
                'totalRevenue' => $expectedCash + $totalCard,
                'totalOrders' => (clone $ordersInPeriod)->count(),
            ];
        } catch (Exception $e) {
            Log::error('Error getting zReport: '.$e->getMessage());

            throw new RuntimeException('Error al obtener el zReport');
        }
    }
}
