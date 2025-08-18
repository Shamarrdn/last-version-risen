<?php
// Simple test file
echo "System Test\n";
echo "==========\n\n";

// Check if files exist
$files = [
    'vendor/autoload.php',
    'config/database.php',
    'app/Models/Product.php',
    'app/Models/ProductSizeColorInventory.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "OK: {$file} exists\n";
    } else {
        echo "ERROR: {$file} missing\n";
    }
}

echo "\nNotes:\n";
echo "- Make sure to run: php artisan migrate\n";
echo "- Check database settings in .env file\n";
echo "- Try creating a new product through the interface\n";
echo "- Check logs in storage/logs/laravel.log\n";
echo "- Test the form submission and check console logs\n";
?>

