<?php

namespace App\Analytics;

interface OrderAnalyticsReader
{
    public function summary(): OrderAnalyticsSummary;
}