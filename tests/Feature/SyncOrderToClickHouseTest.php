<?php

use App\Jobs\SyncOrderToClickHouse;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('dispatches a clickhouse sync job when an order is created', function () {
    Queue::fake();

    $order = Order::factory()->create();

    Queue::assertPushed(SyncOrderToClickHouse::class, function (SyncOrderToClickHouse $job) use ($order): bool {
        return $job->orderId === $order->id;
    });
});
