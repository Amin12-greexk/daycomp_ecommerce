<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomFormController;
use App\Http\Controllers\Admin\DiscountTierController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\CheckoutController;

Route::get('/', [HomeController::class, 'index'])->name('customer.home');
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products/{id}/approve', [ProductController::class, 'approve'])->name('admin.products.approve');
    Route::post('/products/{id}/unapprove', [ProductController::class, 'unapprove'])->name('admin.products.unapprove');
    Route::post('/products/{id}/create-approval', [ProductController::class, 'createApproval'])->name('admin.products.createApproval');
    Route::post('/products/{approval_id}/update-price', [ProductController::class, 'updatePrice'])->name('admin.products.updatePrice');
    Route::get('/products/{product}/custom-forms', [CustomFormController::class, 'index'])->name('admin.custom-forms.index');
    Route::get('/products/{product}/custom-forms/create', [CustomFormController::class, 'create'])->name('admin.custom-forms.create');
    Route::put('/products/{approval_id}/toggle-custom-form', [ProductController::class, 'toggleCustomForm'])->name('admin.products.toggleCustomForm');
    Route::post('/products/{product}/custom-forms', [CustomFormController::class, 'store'])->name('admin.custom-forms.store');
    Route::get('/custom-forms/{id}/edit', [CustomFormController::class, 'edit'])->name('admin.custom-forms.edit');
    Route::put('/custom-forms/{id}', [CustomFormController::class, 'update'])->name('admin.custom-forms.update');
    Route::delete('/custom-forms/{id}', [CustomFormController::class, 'destroy'])->name('admin.custom-forms.destroy');
    Route::post('/checkout/cache', [CheckoutController::class, 'cacheForm'])->name('checkout.cache');
    Route::get('/orders/{order}/resi', [OrderController::class, 'Resi'])->name('orders.Resi');
    Route::get('/orders/{order}/custom-form', [OrderController::class, 'downloadCustomForm'])->name('orders.downloadCustomForm');
    Route::get('/products/check-new', [DashboardController::class, 'checkForNewProducts'])->name('admin.products.check_new');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
});
Route::post('/checkout/cache', [CheckoutController::class, 'cacheForm'])->name('checkout.cache');
Route::post('/products/{approval_id}/toggle-custom-form', [ProductController::class, 'toggleCustomForm'])->name('admin.products.toggleCustomForm');
Route::post('/products/{approval_id}/update-minimum-quantity', [ProductController::class, 'updateMinimumQuantity'])->name('admin.products.updateMinimumQuantity');
Route::get('/products/{product}/discount-tiers', [DiscountTierController::class, 'index'])->name('admin.discount-tiers.index');
Route::get('/products/{product}/discount-tiers/create', [DiscountTierController::class, 'create'])->name('admin.discount-tiers.create');
Route::post('/products/{product}/discount-tiers', [DiscountTierController::class, 'store'])->name('admin.discount-tiers.store');
Route::get('/discount-tiers/{id}/edit', [DiscountTierController::class, 'edit'])->name('admin.discount-tiers.edit');
Route::put('/discount-tiers/{id}', [DiscountTierController::class, 'update'])->name('admin.discount-tiers.update');
Route::delete('/discount-tiers/{id}', [DiscountTierController::class, 'destroy'])->name('admin.discount-tiers.destroy');
Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
Route::get('/checkout', [CheckoutController::class, 'view'])->name('checkout.view');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/thank-you/{order}', [App\Http\Controllers\Customer\ThankYouController::class, 'show'])->name('thankyou');
Route::get('/midtrans/token/{order}', [CheckoutController::class, 'getPaymentToken'])->name('midtrans.token');
Route::post('/midtrans/token/{order}', [CheckoutController::class, 'getMidtransToken'])->name('midtrans.token');
Route::post('/admin/products/{id}/update-short-description', [ProductController::class, 'updateShortDescription'])->name('admin.products.updateShortDescription');
Route::get('/admin/orders/search', [OrderController::class, 'search'])->name('admin.orders.search');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('customer.category');
Route::get('/orders/{order}/resi', [OrderController::class, 'downloadResi'])->name('orders.downloadResi');

Route::get('/api/categories', function () {
    return \App\Models\Category::all();
});
Route::post('/test', function () {
    return response()->json(['message' => 'Test successful']);
})->name('test.route');
Route::get('/category/{id}', [\App\Http\Controllers\Customer\HomeController::class, 'category'])->name('customer.category');

Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/', [CartController::class, 'viewCart'])->name('cart.view');
});
Route::get('/api/monthly-sales', function () {
    $year = request('year', now()->year);

    $salesByMonth = DB::table('order_details')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->whereYear('orders.created_at', $year)
        ->selectRaw('MONTH(orders.created_at) as month,
                     SUM(order_details.price * order_details.quantity) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->map(fn($v) => (float) $v);   // pastikan numeric

    $labels = [];
    $data = [];
    for ($i = 1; $i <= 12; $i++) {
        $labels[] = \Carbon\Carbon::create()->month($i)->format('M');
        $data[] = $salesByMonth[$i] ?? 0;
    }

    return response()->json([
        'labels' => $labels,
        'data' => $data,
    ]);
})->name('api.monthly-sales');

// routes/web.php
Route::get('/api/years', function () {
    return DB::table('orders')
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');
});

Route::get('/test-upload', function () {
    return view('test-upload');
});

// Handle upload ke Sanity
Route::post('/test-upload', [\App\Http\Controllers\Customer\CheckoutController::class, 'testUpload'])->name('test.upload');


