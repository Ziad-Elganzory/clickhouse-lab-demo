<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class OrderAnalyticsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    protected ?string $heading = 'Order analytics (ClickHouse)';

    protected ?string $description = 'Aggregated from order_events via the sync job';

    protected function getStats(): array
    {
        $startOfDay = now()->startOfDay()->format('Y-m-d H:i:s');

        $ordersToday = (int) DB::connection('clickhouse')
            ->table('order_events')
            ->where('ordered_at', '>=', $startOfDay)
            ->count();

        $revenueToday = (float) DB::connection('clickhouse')
            ->table('order_events')
            ->where('ordered_at', '>=', $startOfDay)
            ->sum('amount');

        $totalOrders = (int) DB::connection('clickhouse')
            ->table('order_events')
            ->count();

        $totalRevenue = (float) DB::connection('clickhouse')
            ->table('order_events')
            ->sum('amount');

        return [
            Stat::make('Orders today', Number::format($ordersToday)),
            Stat::make('Revenue today', Number::currency($revenueToday)),
            Stat::make('Total orders', Number::format($totalOrders)),
            Stat::make('Total revenue', Number::currency($totalRevenue)),
        ];
    }
}
