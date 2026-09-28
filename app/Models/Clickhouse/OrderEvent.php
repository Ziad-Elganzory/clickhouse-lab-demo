<?php

namespace App\Models\Clickhouse;

use ClickHouse\Laravel\Eloquent\Model;
use Override;

class OrderEvent extends Model
{
    protected $connection = 'clickhouse';
    protected $table = 'order_events';
    public $incrementing = false;
    public $timestamps = false;
    protected function casts()
    {
        return [
            'amount' => 'decimal:2',
            'ordered_at' => 'datetime',
        ];
    }
}
