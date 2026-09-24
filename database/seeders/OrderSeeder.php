<?php

namespace Database\Seeders;

use App\Jobs\SyncOrderToClickHouse;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::withoutEvents(function (): void {
            Order::factory(10)->today()->create()->each(function (Order $order): void {
                SyncOrderToClickHouse::dispatchSync($order->id);
            });

            Order::factory(290)->past()->create()->each(function (Order $order): void {
                SyncOrderToClickHouse::dispatchSync($order->id);
            });
        });
    }
}
