<?php

use ClickHouse\Laravel\Schema\Blueprint as ClickHouseBlueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'clickhouse';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('clickhouse')->create('order_events', function (ClickHouseBlueprint $table) {
            $table->unsignedBigInteger('order_id');
            $table->string('customer_name');
            $table->decimal('amount', 12, 2);
            $table->string('status', 32);
            $table->dateTime('ordered_at');
            $table->engine('MergeTree()');
            $table->orderBy(['ordered_at', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('clickhouse')->dropIfExists('order_events');
    }
};
