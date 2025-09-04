<?php

// #####################################################################
// THIS IS THE CORRECTED CONTROLLER FOR YOUR E-COMMERCE PROJECT
// File: app/Http/Controllers/Admin/DiscountTierController.php
// #####################################################################

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DiscountTier;

class DiscountTierController extends Controller
{
    /**
     * Display a listing of the discount tiers for a specific product.
     */
    public function index(Product $product)
    {
        $discountTiers = $product->discountTiers()->orderBy('min_quantity')->get();
        return view('admin.discount_tiers.index', compact('product', 'discountTiers'));
    }

    /**
     * Show the form for creating a new discount tier.
     */
    public function create(Product $product)
    {
        return view('admin.discount_tiers.create', compact('product'));
    }

    /**
     * Store a newly created discount tier in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'min_quantity' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $product->discountTiers()->create($request->all());

        // THE FIX: Redirect to the correct nested route name.
        return redirect()->route('admin.products.discount-tiers.index', $product->id)
            ->with('success', 'Discount tier added successfully.');
    }

    /**
     * Show the form for editing the specified discount tier.
     */
    public function edit($id)
    {
        // Eager load the product relationship to get the name for the view
        $discountTier = DiscountTier::with('product')->findOrFail($id);
        return view('admin.discount_tiers.edit', compact('discountTier'));
    }

    /**
     * Update the specified discount tier in storage.
     */
    public function update(Request $request, $id)
    {
        $discountTier = DiscountTier::findOrFail($id);

        $request->validate([
            'min_quantity' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $discountTier->update($request->all());

        // THE FIX: Redirect to the correct nested route name.
        return redirect()->route('admin.products.discount-tiers.index', $discountTier->product_id)
            ->with('success', 'Discount tier updated successfully.');
    }

    /**
     * Remove the specified discount tier from storage.
     */
    public function destroy($id)
    {
        $discountTier = DiscountTier::findOrFail($id);
        $discountTier->delete();

        return redirect()->back()->with('success', 'Discount tier deleted successfully.');
    }
}