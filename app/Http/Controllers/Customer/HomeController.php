<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::whereHas('approval', function ($query) {
            $query->where('is_approved', 1);
        })->with('approval', 'discountTiers');

        if ($request->filled('min_price')) {
            $query->whereHas('approval', function ($q) use ($request) {
                $q->where('custom_price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('approval', function ($q) use ($request) {
                $q->where('custom_price', '<=', $request->max_price);
            });
        }

        $products = $query->get();
        $categories = Category::all();

        return view('customer.home', compact('products', 'categories'));
    }

    public function category($id)
    {
        $products = Product::where('category_id', $id)
            ->whereHas('approval', function ($query) {
                $query->where('is_approved', 1);
            })
            ->with('approval', 'discountTiers')
            ->get();

        $categories = Category::all();

        return view('customer.home', compact('products', 'categories'));
    }

}
