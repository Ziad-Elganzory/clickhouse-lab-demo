<?php

namespace App\Analytics;

use App\Models\Order;

class MysqlOrderAnalyticsReader implements OrderAnalyticsReader
{
    public function summary(): OrderAnalyticsSummary
    {
        $startOfDay = now()->startOfDay()->format('Y-m-d H:i:s');

        $row = Order::query()
            ->toBase()
            ->selectRaw('
                count(case when created_at >= ? then 1 end) as orders_today,
                coalesce(sum(case when created_at >= ? then amount else 0 end), 0) as revenue_today,
                count(*) as total_orders,
                coalesce(sum(amount), 0) as total_revenue
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