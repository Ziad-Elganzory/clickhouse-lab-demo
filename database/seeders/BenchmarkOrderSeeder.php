<?php

namespace Database\Seeders;

use ClickHouse\Enums\Format;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use ClickHouse\Laravel\Query\Builder as ClickHouseBuilder;

class BenchmarkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $total = 1_000_000;
        $chunk = 5_000;
        $statuses = ['pending', 'paid', 'cancelled', 'refunded'];

        // Optional: wipe existing demo data first
        DB::table('orders')->truncate();
        DB::connection('clickhouse')->statement('TRUNCATE TABLE order_events');

        $nextId = 1;

        for ($written = 0; $written < $total; $written += $chunk) {
            $size = min($chunk, $total - $written);
            $mysqlRows = [];
            $clickhouseRows = [];

            for ($i = 0; $i < $size; $i++) {
                $id = $nextId++;
                $createdAt = now()
                    ->subDays(random_int(0, 90))
                    ->subSeconds(random_int(0, 86400))
                    ->format('Y-m-d H:i:s');
                $amount = round(mt_rand(1000, 50000) / 100, 2); // 10.00–500.00
                $status = $statuses[array_rand($statuses)];
                $name = 'Customer '.$id;

                $mysqlRows[] = [
                    'id' => $id,
                    'customer_name' => $name,
                    'amount' => $amount,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $clickhouseRows[] = [
                    'order_id' => $id,
                    'customer_name' => $name,
                    'amount' => $amount,
                    'status' => $status,
                    'ordered_at' => $createdAt,
                ];
            }

            DB::table('orders')->insert($mysqlRows);
            /** @var ClickHouseBuilder $builder */
            $builder = DB::connection('clickhouse')->table('order_events');
            $builder->insert($clickhouseRows, Format::JSONEachRow);
            
            if ($written % 50_000 === 0) {
                $this->command?->info("Inserted {$written} / {$total}");
            }
        }

        // Reset MySQL auto-increment after explicit IDs
        DB::statement('ALTER TABLE orders AUTO_INCREMENT = '.($total + 1));

        $this->command?->info('Done: 1,000,000 rows in MySQL + ClickHouse');
    }
}