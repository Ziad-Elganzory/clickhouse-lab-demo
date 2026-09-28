<?php

namespace App\Analytics;

use App\Models\Clickhouse\OrderEvent;

class ClickHouseOrderAnalyticsReader implements OrderAnalyticsReader
{
    public function summary(): OrderAnalyticsSummary
    {
        $startOfDay = now()->startOfDay()->format('Y-m-d H:i:s');

        $row = (object) OrderEvent::query()
            ->selectRaw('
                countIf(ordered_at >= ?) as orders_today,
                sumIf(amount, ordered_at >= ?) as revenue_today,
                count() as total_orders,
                sum(amount) as total_revenue
            ', [$startOfDay, $startOfDay])
            ->first();

        return new OrderAnalyticsSummary(
            ordersToday: (int) $row->orders_today,
            revenueToday: (float) $row->revenue_today,
            totalOrders: (int) $row->total_orders,
            totalRevenue: (float) $row->total_revenue,
        );
    }
}