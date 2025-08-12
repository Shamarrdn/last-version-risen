<?php

require_once 'vendor/autoload.php';

use App\Services\ReportService;
use App\Models\Order;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing ReportService...\n";

try {
    $service = new ReportService();
    $report = $service->getSalesReport('month');
    
    echo "Total Sales: " . $report['total_sales'] . "\n";
    echo "Orders Count: " . $report['orders_count'] . "\n";
    echo "Top Products Count: " . $report['top_products']->count() . "\n";
    echo "Growth Percentage: " . $report['growth']['percentage'] . "%\n";
    
    echo "Test completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
