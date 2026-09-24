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
    
        $clickhouse = (object) DB::connection('clickhouse')
            ->table('order_events')
            ->selectRaw('
                countIf(ordered_at >= ?) as orders_today,
                sumIf(amount, ordered_at >= ?) as revenue_today,
                count() as total_orders,
                sum(amount) as total_revenue
            ', [$startOfDay, $startOfDay])
            ->first();
    
        // MySQL returns stdClass from first() — no (object) cast needed
        $mysql = DB::table('orders')
            ->selectRaw('
                count(case when created_at >= ? then 1 end) as orders_today,
                coalesce(sum(case when created_at >= ? then amount else 0 end), 0) as revenue_today,
                count(*) as total_orders,
                coalesce(sum(amount), 0) as total_revenue
            ', [$startOfDay, $startOfDay])
            ->first();
    
        return [
            Stat::make('Orders today (CH)', Number::format((int) $clickhouse->orders_today)),
            Stat::make('Revenue today (CH)', Number::currency((float) $clickhouse->revenue_today)),
            Stat::make('Total orders (CH)', Number::format((int) $clickhouse->total_orders)),
            Stat::make('Total revenue (CH)', Number::currency((float) $clickhouse->total_revenue)),
    
            Stat::make('Orders today (MySQL)', Number::format((int) $mysql->orders_today)),
            Stat::make('Revenue today (MySQL)', Number::currency((float) $mysql->revenue_today)),
            Stat::make('Total orders (MySQL)', Number::format((int) $mysql->total_orders)),
            Stat::make('Total revenue (MySQL)', Number::currency((float) $mysql->total_revenue)),
        ];
    }
}
