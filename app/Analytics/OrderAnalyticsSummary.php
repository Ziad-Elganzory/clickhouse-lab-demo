<?php

namespace App\Analytics;

final class OrderAnalyticsSummary
{
    public function __construct(
        public int $ordersToday,
        public float $revenueToday,
        public int $totalOrders,
        public float $totalRevenue,
    ) {}
}