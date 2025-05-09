<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::whereHas('approval', function ($query) {
            $query->where('is_approved', 1);
        })->with('approval', 'discountTiers')->get();

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
