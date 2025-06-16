<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\DiscountTier;
use Illuminate\Http\Request; // Import the Request class
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalDiscounts = DiscountTier::count();

        // Get the timestamp of the latest product for the initial check
        $lastProductTimestamp = Product::latest()->first()?->created_at->toIso8601String();

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalDiscounts', 'lastProductTimestamp'));
    }

    /**
     * Check for new products since the last timestamp.
     * This method will be called by JavaScript.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkForNewProducts(Request $request)
    {
        // Get the last timestamp from the request, default to now if not provided
        $lastCheck = $request->input('since', now()->toIso8601String());

        // Parse the timestamp safely
        try {
            $since = Carbon::parse($lastCheck)->addSecond(); // Check for products created after this time
        } catch (\Exception $e) {
            $since = now();
        }

        // Find any products created after the 'since' timestamp
        $newProducts = Product::where('created_at', '>=', $since)->get();

        // Return the new products and the latest timestamp for the next check
        return response()->json([
            'newProducts' => $newProducts,
            'latestTimestamp' => Product::latest()->first()?->created_at->toIso8601String() ?? now()->toIso8601String()
        ]);
    }
}
