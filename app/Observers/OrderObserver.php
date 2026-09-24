<?php

namespace App\Observers;

use App\Jobs\SyncOrderToClickHouse;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        SyncOrderToClickHouse::dispatch($order->id);
    }
}
