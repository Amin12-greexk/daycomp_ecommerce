<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\Admin\SyncController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- CUSTOMER FACING ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('customer.home');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('customer.category');

Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/', [CartController::class, 'viewCart'])->name('cart.view');
});

Route::get('/checkout', [CheckoutController::class, 'view'])->name('checkout.view');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/cache', [CheckoutController::class, 'cacheForm'])->name('checkout.cache');
// Add this line in routes/web.php with your other customer routes

Route::get('/orders/{order}/download-resi', [CheckoutController::class, 'downloadResi'])->name('orders.downloadResi');
Route::get('/thank-you/{order}', [App\Http\Controllers\Customer\ThankYouController::class, 'show'])->name('thankyou');


// --- ADMIN AUTH ROUTES ---
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');


// --- ADMIN PROTECTED ROUTES ---
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report/pdf', [DashboardController::class, 'downloadReport'])->name('dashboard.download_report');

    // Product Management Routes
    Route::post('products/{id}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{id}/unapprove', [ProductController::class, 'unapprove'])->name('products.unapprove');
    Route::post('products/{id}/toggle-custom-form', [ProductController::class, 'toggleCustomForm'])->name('products.toggleCustomForm');
    Route::resource('products', ProductController::class)->except(['create', 'store', 'show']);

    // Nested Resource Routes for Forms and Discounts
    Route::resource('products.custom-forms', CustomFormController::class)->shallow();
    Route::resource('products.discount-tiers', DiscountTierController::class)->shallow();

    // Sync Routes
    Route::get('/sync-products', [SyncController::class, 'index'])->name('sync.index');
    Route::post('/sync-products', [SyncController::class, 'sync'])->name('sync.run');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}/resi', [OrderController::class, 'downloadResi'])->name('orders.downloadResi');
    Route::get('/orders/{order}/custom-form', [OrderController::class, 'downloadCustomForm'])->name('orders.downloadCustomForm');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/sync-reports/{report}/download', [SyncController::class, 'downloadPdf'])->name('sync.download');

});


// --- MISC & API ROUTES ---
Route::get('/api/monthly-sales', function () {
    $year = request('year', now()->year);
    $salesByMonth = DB::table('order_details')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->whereYear('orders.created_at', $year)
        ->selectRaw('MONTH(orders.created_at) as month, SUM(order_details.price * order_details.quantity) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->map(fn($v) => (float) $v);

    $labels = [];
    $data = [];
    for ($i = 1; $i <= 12; $i++) {
        $labels[] = \Carbon\Carbon::create()->month($i)->format('M');
        $data[] = $salesByMonth[$i] ?? 0;
    }
    return response()->json(['labels' => $labels, 'data' => $data]);
})->name('api.monthly-sales');

Route::get('/api/years', function () {
    return DB::table('orders')
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');
});