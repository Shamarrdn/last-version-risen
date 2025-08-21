<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\{
    ProductController,
    OrderController,
    CartController,
    CheckoutController,
    ProfileController,
    NotificationController,
    DashboardController,
    PhoneController,
    AddressController,
    ContactController,
    PolicyController,
    HomeController,
};

// Admin Controllers
use App\Http\Controllers\Admin\{
    OrderController as AdminOrderController,
    ProductController as AdminProductController,
    CategoryController as AdminCategoryController,
    ReportController,
    DashboardController as AdminDashboardController,
    SuperAdminDashboardController,
    UserManagementController,
    RoleManagementController,
    CouponController,
    QuantityDiscountController,
    SizesColorsController
};

// Additional Controllers
use App\Http\Controllers\OrderFriendController;
use App\Http\Controllers\OrderTransferController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Debug route - مؤقت للفحص
Route::get('/debug-product-availability', function() {
    $products = \App\Models\Product::where('name', 'like', '%أحمد%')
        ->orWhere('slug', 'like', '%ahmd%')
        ->with(['inventory.size', 'inventory.color'])
        ->get();
    
    if ($products->isEmpty()) {
        $products = \App\Models\Product::take(3)->with(['inventory.size', 'inventory.color'])->get();
    }
    
    $html = '<h2>فحص حالة المنتجات</h2>';
    
    foreach ($products as $product) {
        $html .= '<div style="border: 1px solid #ccc; padding: 20px; margin: 10px;">';
        $html .= '<h3>' . $product->name . '</h3>';
        $html .= '<p><strong>Slug:</strong> ' . $product->slug . '</p>';
        $html .= '<p><strong>متوفر:</strong> ' . ($product->is_available ? 'نعم ✅' : 'لا ❌') . '</p>';
        $html .= '<p><strong>المخزون الأساسي:</strong> ' . $product->stock . '</p>';
        $html .= '<p><strong>المخزون المستهلك:</strong> ' . ($product->consumed_stock ?? 0) . '</p>';
        $html .= '<p><strong>المخزون المتاح:</strong> ' . $product->available_stock . '</p>';
        
        $html .= '<h4>المخزون التفصيلي (' . $product->inventory->count() . ' عنصر):</h4>';
        
        if ($product->inventory->count() > 0) {
            $html .= '<table border="1" style="width:100%; border-collapse:collapse;">';
            $html .= '<tr><th>المقاس</th><th>اللون</th><th>الكمية</th><th>المستهلك</th><th>المتاح</th><th>متوفر</th><th>السعر</th></tr>';
            
            foreach ($product->inventory as $item) {
                $html .= '<tr>';
                $html .= '<td>' . ($item->size ? $item->size->name : 'غير محدد') . '</td>';
                $html .= '<td>' . ($item->color ? $item->color->name : 'غير محدد') . '</td>';
                $html .= '<td>' . $item->stock . '</td>';
                $html .= '<td>' . ($item->consumed_stock ?? 0) . '</td>';
                $html .= '<td>' . $item->available_stock . '</td>';
                $html .= '<td>' . ($item->is_available ? 'نعم ✅' : 'لا ❌') . '</td>';
                $html .= '<td>' . $item->price . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        } else {
            $html .= '<p>لا يوجد مخزون تفصيلي</p>';
        }
        
        $html .= '</div>';
    }
    
    return $html;
});

// Route لإصلاح حالة توفر المنتجات - مؤقت
Route::get('/fix-product-availability', function() {
    $fixedCount = 0;
    $products = \App\Models\Product::with('inventory')->get();
    
    foreach ($products as $product) {
        $oldStatus = $product->is_available;
        
        // التحقق من وجود مخزون متاح
        $hasAvailableStock = $product->inventory()
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->whereRaw('stock > COALESCE(consumed_stock, 0)')
            ->exists();
        
        // إذا كان هناك مخزون متاح ولكن المنتج غير متوفر، قم بتفعيله
        if ($hasAvailableStock && !$product->is_available) {
            $product->is_available = true;
            $product->save();
            $fixedCount++;
        }
        // إذا لم يكن هناك مخزون متاح ولكن المنتج متوفر، قم بإلغاء تفعيله
        elseif (!$hasAvailableStock && $product->is_available) {
            $product->is_available = false;
            $product->save();
            $fixedCount++;
        }
    }
    
    return "تم إصلاح حالة $fixedCount منتج.";
});

// Route للتشخيص المباشر لمنتج محدد
Route::get('/debug-product/{slug}', function($slug) {
    $product = \App\Models\Product::where('slug', $slug)->with(['inventory.size', 'inventory.color'])->first();
    
    if (!$product) {
        return "المنتج غير موجود: $slug";
    }
    
    $html = '<div style="font-family: Arial; direction: rtl; text-align: right;">';
    $html .= '<h2>تشخيص منتج: ' . $product->name . '</h2>';
    
    $html .= '<div style="background: #f8f9fa; padding: 20px; margin: 10px 0; border-radius: 5px;">';
    $html .= '<h3>معلومات أساسية</h3>';
    $html .= '<p><strong>الاسم:</strong> ' . $product->name . '</p>';
    $html .= '<p><strong>Slug:</strong> ' . $product->slug . '</p>';
    $html .= '<p><strong>متوفر:</strong> ' . ($product->is_available ? 'نعم ✅' : 'لا ❌') . '</p>';
    $html .= '<p><strong>تمكين اختيار المقاس:</strong> ' . ($product->enable_size_selection ? 'نعم' : 'لا') . '</p>';
    $html .= '<p><strong>تمكين اختيار اللون:</strong> ' . ($product->enable_color_selection ? 'نعم' : 'لا') . '</p>';
    $html .= '</div>';
    
    $html .= '<div style="background: #e3f2fd; padding: 20px; margin: 10px 0; border-radius: 5px;">';
    $html .= '<h3>الألوان المتاحة (' . $product->available_colors->count() . ')</h3>';
    if ($product->available_colors->count() > 0) {
        foreach ($product->available_colors as $color) {
            $html .= '<p>- ' . $color->name . ' (ID: ' . $color->id . ')</p>';
        }
    } else {
        $html .= '<p style="color: red;">لا توجد ألوان متاحة</p>';
    }
    $html .= '</div>';
    
    $html .= '<div style="background: #f3e5f5; padding: 20px; margin: 10px 0; border-radius: 5px;">';
    $html .= '<h3>المقاسات المتاحة (' . $product->available_sizes->count() . ')</h3>';
    if ($product->available_sizes->count() > 0) {
        foreach ($product->available_sizes as $size) {
            $html .= '<p>- ' . $size->name . ' (ID: ' . $size->id . ')</p>';
        }
    } else {
        $html .= '<p style="color: red;">لا توجد مقاسات متاحة</p>';
    }
    $html .= '</div>';
    
    $html .= '<div style="background: #fff3e0; padding: 20px; margin: 10px 0; border-radius: 5px;">';
    $html .= '<h3>تفاصيل المخزون (' . $product->inventory->count() . ' عنصر)</h3>';
    if ($product->inventory->count() > 0) {
        $html .= '<table border="1" style="width:100%; border-collapse:collapse; text-align: center;">';
        $html .= '<tr><th>المقاس</th><th>اللون</th><th>الكمية</th><th>المستهلك</th><th>المتاح</th><th>متوفر</th><th>السعر</th></tr>';
        
        foreach ($product->inventory as $item) {
            $availableStock = max(0, $item->stock - ($item->consumed_stock ?? 0));
            $html .= '<tr>';
            $html .= '<td>' . ($item->size ? $item->size->name : 'غير محدد') . '</td>';
            $html .= '<td>' . ($item->color ? $item->color->name : 'غير محدد') . '</td>';
            $html .= '<td>' . $item->stock . '</td>';
            $html .= '<td>' . ($item->consumed_stock ?? 0) . '</td>';
            $html .= '<td style="font-weight: bold; color: ' . ($availableStock > 0 ? 'green' : 'red') . '">' . $availableStock . '</td>';
            $html .= '<td>' . ($item->is_available ? 'نعم ✅' : 'لا ❌') . '</td>';
            $html .= '<td>' . $item->price . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    } else {
        $html .= '<p style="color: red;">لا يوجد مخزون</p>';
    }
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
});

// Static Pages Routes
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/thank-you', function () {
    return view('thank-you');
})->name('thank-you');

Route::get('/shop', function () {
    return view('shop');
})->name('shop');

// Products Routes (Public)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::post('/filter', [ProductController::class, 'filter'])->name('filter');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/details', [ProductController::class, 'getProductDetails'])->name('details');
    
    // Product Variants API (Public)
    Route::get('/{product}/sizes-for-color', [ProductController::class, 'getSizesForColor'])->name('sizes-for-color');
    Route::get('/{product}/colors-for-size', [ProductController::class, 'getColorsForSize'])->name('colors-for-size');
    Route::get('/{product}/variant-details', [ProductController::class, 'getVariantDetails'])->name('variant-details');
});

// Auth Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Common Routes (for all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Customer Routes
    Route::middleware(['role:customer'])->group(function () {


        // Phones
        Route::post('/phones', [PhoneController::class, 'store']);
        Route::get('/phones/{phone}', [PhoneController::class, 'show']);
        Route::put('/phones/{phone}', [PhoneController::class, 'update']);
        Route::delete('/phones/{phone}', [PhoneController::class, 'destroy']);
        Route::post('/phones/{phone}/make-primary', [PhoneController::class, 'makePrimary']);

        // Addresses
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{address}/make-primary', [AddressController::class, 'makePrimary']);

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [ProductController::class, 'addToCart'])->name('add');
            Route::get('/items', [ProductController::class, 'getCartItems'])->name('items');
            Route::patch('/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('update');
            Route::delete('/remove/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });


        


        // Checkout
        Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store')->middleware('web');
            Route::post('/apply-coupon', 'applyCoupon')->name('apply-coupon');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order:uuid}', [OrderController::class, 'show'])->name('show');
            
            // Customer Order Friends Management
            Route::post('/{order:uuid}/friends', [OrderFriendController::class, 'addFriend'])->name('friends.add');
            Route::delete('/{order:uuid}/friends/{friend}', [OrderFriendController::class, 'removeFriend'])->name('friends.remove');
            Route::get('/{order:uuid}/friends', [OrderFriendController::class, 'getFriends'])->name('friends.list');
        });
    });

    // Friend Access Routes (Public)
    Route::get('/order-access/{accessToken}', [OrderFriendController::class, 'accessOrder'])->name('orders.friend-access');
    Route::post('/order-access/{accessToken}/status', [OrderFriendController::class, 'updateOrderStatus'])->name('orders.friend-update-status');

    // SuperAdmin Routes
    Route::middleware(['auth', 'role:superadmin', \App\Http\Middleware\AdminPopupAuthMiddleware::class])
        ->prefix('superadmin')
        ->name('superadmin.')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/admin-sales-report', [SuperAdminDashboardController::class, 'adminSalesReport'])->name('admin-sales-report');
            Route::post('/update-fcm-token', [SuperAdminDashboardController::class, 'updateFcmToken'])->name('update-fcm-token');
            
            // User Management
            Route::resource('users', UserManagementController::class);
            Route::post('/users/{user}/change-role', [UserManagementController::class, 'changeRole'])->name('users.change-role');
            
            // Role Management
            Route::resource('roles', RoleManagementController::class);
            
            // Shared Admin Functions (Products, Categories, Orders, Reports, etc.)
            // Products Management  
            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('products', AdminProductController::class);
                Route::resource('categories', AdminCategoryController::class);
                Route::get('/products/inventory/status', [AdminProductController::class, 'inventory'])->name('products.inventory');
                Route::delete('/products/{product}/inventory/{inventory}', [AdminProductController::class, 'deleteInventory'])->name('products.inventory.delete');
            });

            // Coupons & Discounts Management
            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('coupons', CouponController::class);
                Route::post('coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
                Route::resource('quantity-discounts', QuantityDiscountController::class);
            });

            // Sizes & Colors Management
            Route::prefix('sizes-colors')->name('sizes-colors.')->group(function () {
                Route::get('/', [SizesColorsController::class, 'superadminIndex'])->name('index');
                Route::post('/sizes', [SizesColorsController::class, 'storeSize'])->name('sizes.store');
                Route::put('/sizes/{id}', [SizesColorsController::class, 'updateSize'])->name('sizes.update');
                Route::delete('/sizes/{id}', [SizesColorsController::class, 'destroySize'])->name('sizes.destroy');
                Route::get('/sizes/{id}', [SizesColorsController::class, 'getSize'])->name('sizes.show');
                Route::get('/sizes', [SizesColorsController::class, 'getSizes'])->name('sizes.list');
                
                Route::post('/colors', [SizesColorsController::class, 'storeColor'])->name('colors.store');
                Route::put('/colors/{id}', [SizesColorsController::class, 'updateColor'])->name('colors.update');
                Route::delete('/colors/{id}', [SizesColorsController::class, 'destroyColor'])->name('colors.destroy');
                Route::get('/colors/{id}', [SizesColorsController::class, 'getColor'])->name('colors.show');
                Route::get('/colors', [SizesColorsController::class, 'getColors'])->name('colors.list');
            });

            // Orders Management
            Route::middleware(['permission:manage orders'])->group(function () {
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
                Route::put('/orders/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
                Route::put('/orders/{order:uuid}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
                    ->name('orders.update-payment-status');
                Route::patch('/orders/{order:uuid}/payment', [AdminOrderController::class, 'updatePayment'])
                    ->name('orders.update-payment');
                Route::get('/sales-statistics', [AdminOrderController::class, 'salesStatistics'])->name('sales.statistics');
            });

            // Reports Management
            Route::middleware(['permission:manage reports'])->group(function () {
                Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            });
        });
        
    // Admin Routes
    Route::middleware(['auth', 'role:admin|superadmin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard/', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Products Management
            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('products', AdminProductController::class);
                Route::resource('categories', AdminCategoryController::class);
                Route::delete('/products/{product}/inventory/{inventory}', [AdminProductController::class, 'deleteInventory'])->name('products.inventory.delete');
            });

            // Coupons & Discounts Management
            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('coupons', CouponController::class);
                Route::post('coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
            });

            // Orders Management
            Route::middleware(['permission:manage orders'])->group(function () {
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/assigned', [AdminOrderController::class, 'assignedOrders'])->name('orders.assigned');
                Route::get('/orders/unassigned', [AdminOrderController::class, 'unassignedOrders'])->name('orders.unassigned');
                Route::get('/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
                Route::put('/orders/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
                Route::put('/orders/{order:uuid}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
                    ->name('orders.update-payment-status');
                Route::patch('/orders/{order:uuid}/payment', [AdminOrderController::class, 'updatePayment'])
                    ->name('orders.update-payment');

                // Order Assignment Routes
                Route::post('/orders/{order:uuid}/assign', [AdminOrderController::class, 'assignOrder'])
                    ->name('orders.assign');
                Route::delete('/orders/{order:uuid}/unassign', [AdminOrderController::class, 'unassignOrder'])
                    ->name('orders.unassign');
            
                Route::get('/sales-statistics', [AdminOrderController::class, 'salesStatistics'])->name('sales.statistics');

                // Order Friends Management
                Route::post('/orders/{order:uuid}/friends', [OrderFriendController::class, 'addFriend'])->name('orders.friends.add');
                Route::delete('/orders/{order:uuid}/friends/{friend}', [OrderFriendController::class, 'removeFriend'])->name('orders.friends.remove');
                Route::get('/orders/{order:uuid}/friends', [OrderFriendController::class, 'getFriends'])->name('orders.friends.list');
                
                // Invited Orders for Admins
                Route::get('/invited-orders', [OrderFriendController::class, 'showInvitedOrders'])->name('orders.invited');
                Route::get('/invited-orders/data', [OrderFriendController::class, 'getInvitedOrders'])->name('orders.invited.data');
                
                // Order Transfer Management
                Route::get('/orders/{order:uuid}/transfer', [OrderTransferController::class, 'create'])->name('orders.transfer.create');
                Route::post('/orders/{order:uuid}/transfer', [OrderTransferController::class, 'store'])->name('orders.transfer.store');
            });

            // Reports Management
            Route::middleware(['permission:manage reports'])->group(function () {
                Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            });

            // Transfer Requests Routes
            Route::middleware(['permission:manage orders'])->group(function () {
                Route::get('/transfers', [OrderTransferController::class, 'index'])->name('transfers.index');
                Route::post('/transfers/{transferRequest}/approve', [OrderTransferController::class, 'approve'])->name('transfers.approve');
                Route::post('/transfers/{transferRequest}/reject', [OrderTransferController::class, 'reject'])->name('transfers.reject');
                Route::delete('/transfers/{transferRequest}/cancel', [OrderTransferController::class, 'cancel'])->name('transfers.cancel');
                Route::get('/transfers/admins', [OrderTransferController::class, 'getAdmins'])->name('transfers.admins');
            });

            // Quantity Discounts Routes
            Route::resource('quantity-discounts', QuantityDiscountController::class);

            // Sizes & Colors Management
            Route::prefix('sizes-colors')->name('sizes-colors.')->group(function () {
                Route::get('/', [SizesColorsController::class, 'index'])->name('index');
                Route::post('/sizes', [SizesColorsController::class, 'storeSize'])->name('sizes.store');
                Route::put('/sizes/{id}', [SizesColorsController::class, 'updateSize'])->name('sizes.update');
                Route::delete('/sizes/{id}', [SizesColorsController::class, 'destroySize'])->name('sizes.destroy');
                Route::get('/sizes/{id}', [SizesColorsController::class, 'getSize'])->name('sizes.show');
                Route::get('/sizes', [SizesColorsController::class, 'getSizes'])->name('sizes.list');
                
                Route::post('/colors', [SizesColorsController::class, 'storeColor'])->name('colors.store');
                Route::put('/colors/{id}', [SizesColorsController::class, 'updateColor'])->name('colors.update');
                Route::delete('/colors/{id}', [SizesColorsController::class, 'destroyColor'])->name('colors.destroy');
                Route::get('/colors/{id}', [SizesColorsController::class, 'getColor'])->name('colors.show');
                Route::get('/colors', [SizesColorsController::class, 'getColors'])->name('colors.list');
            });

            // مسار صفحة المخزون
            Route::get('/products/inventory/status', [AdminProductController::class, 'inventory'])->name('products.inventory');
            
            // Inventory Management
            Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class);
        });
});

// Super Admin Tracking Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/tracking', [App\Http\Controllers\Admin\SuperAdminTrackingController::class, 'index'])->name('tracking');
    Route::get('/tracking/performance', [App\Http\Controllers\Admin\SuperAdminTrackingController::class, 'getAdminPerformance'])->name('tracking.performance');
});


// Protected Cart Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/items', [ProductController::class, 'getCartItems'])->name('cart.items');
    Route::patch('/cart/items/{cartItem}', [ProductController::class, 'updateCartItem'])->name('cart.update-item');
    Route::delete('/cart/remove/{cartItem}', [ProductController::class, 'removeCartItem'])->name('cart.remove-item');
});

// مسارات لوحة تحكم العميل
Route::middleware('client')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // ... باقي مسارات العميل
});



Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// مسارات السلة
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'updateItem'])->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.items.remove');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::post('/check-inventory', [ProductController::class, 'checkInventory'])->name('check.inventory');
});

Route::get('/policy', [PolicyController::class, 'index'])->name('policy');
Route::post('/admin/update-fcm-token', [App\Http\Controllers\Admin\DashboardController::class, 'updateFcmToken'])
    ->middleware(['auth', 'admin']);
