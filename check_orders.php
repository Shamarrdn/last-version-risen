<?php

require_once 'vendor/autoload.php';

use App\Models\Order;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Orders Data...\n";

try {
    $orders = Order::all();
    
    echo "Total Orders: " . $orders->count() . "\n";
    
    if ($orders->count() > 0) {
        echo "\nOrder Details:\n";
        foreach ($orders as $order) {
            echo "Order ID: " . $order->id . "\n";
            echo "Order Status: " . $order->order_status . "\n";
            echo "Payment Status: " . $order->payment_status . "\n";
            echo "Total Amount: " . $order->total_amount . "\n";
            echo "Created At: " . $order->created_at . "\n";
            echo "---\n";
        }
    }
    
    // Check constants
    echo "\nConstants:\n";
    echo "ORDER_STATUS_COMPLETED: " . Order::ORDER_STATUS_COMPLETED . "\n";
    echo "PAYMENT_STATUS_PAID: " . Order::PAYMENT_STATUS_PAID . "\n";
    
    // Check completed and paid orders
    $completedOrders = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count();
    $paidOrders = Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->count();
    
    echo "\nCompleted Orders: " . $completedOrders . "\n";
    echo "Paid Orders: " . $paidOrders . "\n";
    
    echo "Check completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
