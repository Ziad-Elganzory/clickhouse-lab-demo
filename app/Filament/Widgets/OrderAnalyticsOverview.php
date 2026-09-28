<?php

namespace App\Filament\Widgets;

use App\Analytics\OrderAnalyticsReader;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderAnalyticsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    protected ?string $heading = 'Order analytics';

    protected ?string $description = 'Driver selected via ANALYTICS_DRIVER';

    protected function getStats(): array
    {
        $stats = app(OrderAnalyticsReader::class)->summary();

        return [
            Stat::make('Orders today', Number::format($stats->ordersToday)),
            Stat::make('Revenue today', Number::currency($stats->revenueToday)),
            Stat::make('Total orders', Number::format($stats->totalOrders)),
            Stat::make('Total revenue', Number::currency($stats->totalRevenue)),
        ];
    }
}