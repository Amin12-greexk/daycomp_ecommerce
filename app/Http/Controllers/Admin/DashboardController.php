<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\DiscountTier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalDiscounts = DiscountTier::count();

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalDiscounts'));
    }


}
