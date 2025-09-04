<?php

// #####################################################################
// THIS IS THE CORRECTED CONTROLLER FOR YOUR E-COMMERCE PROJECT
// File: app/Http/Controllers/Customer/HomeController.php
// #####################################################################

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage with a list of approved products.
     * Includes filtering by price.
     */
    public function index(Request $request)
    {
        // Start the query by getting only approved products.
        // This replaces the old `whereHas('approval', ...)` logic.
        $query = Product::where('is_approved', true)->with('discountTiers');

        // Filter by minimum price, now checking the 'sale_price' column directly.
        if ($request->filled('min_price')) {
            $query->where('sale_price', '>=', $request->min_price);
        }

        // Filter by maximum price, also checking the 'sale_price' column.
        if ($request->filled('max_price')) {
            $query->where('sale_price', '<=', $request->max_price);
        }

        // Execute the query.
        $products = $query->latest()->get();
        $categories = Category::all();

        return view('customer.home', compact('products', 'categories'));
    }

    /**
     * Display a list of approved products for a specific category.
     */
    public function category($id)
    {
        // The logic is simplified here as well.
        // We get products for the given category_id AND where is_approved is true.
        $products = Product::where('category_id', $id)
            ->where('is_approved', true)
            ->with('discountTiers')
            ->latest()
            ->get();

        $categories = Category::all();

        return view('customer.home', compact('products', 'categories'));
    }
}