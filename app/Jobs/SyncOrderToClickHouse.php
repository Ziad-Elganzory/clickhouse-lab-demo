<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SyncOrderToClickHouse implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [1, 5, 10];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $orderId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::query()->findOrFail($this->orderId);

        DB::connection('clickhouse')->table('order_events')->insert([
            'order_id' => $order->id,
            'customer_name' => $order->customer_name,
            'amount' => $order->amount,
            'status' => $order->status,
            'ordered_at' => $order->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
