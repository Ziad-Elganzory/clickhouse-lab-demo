<?php

return [
    'driver' => env('ANALYTICS_DRIVER', 'clickhouse'),

    'readers' => [
        'clickhouse' => App\Analytics\ClickHouseOrderAnalyticsReader::class,
        'mysql' => App\Analytics\MySQLOrderAnalyticsReader::class,
    ]
];