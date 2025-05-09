<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DiscountTier;

class DiscountTierController extends Controller
{
    public function index(Product $product)
    {
        $discountTiers = $product->discountTiers()->orderBy('min_quantity')->get();
        return view('admin.discount_tiers.index', compact('product', 'discountTiers'));
    }

    public function create(Product $product)
    {
        return view('admin.discount_tiers.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'min_quantity' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $product->discountTiers()->create($request->all());

        return redirect()->route('admin.discount-tiers.index', $product->id)
                         ->with('success', 'Discount tier added successfully.');
    }

    public function edit($id)
    {
        $discountTier = DiscountTier::findOrFail($id);
        return view('admin.discount_tiers.edit', compact('discountTier'));
    }

    public function update(Request $request, $id)
    {
        $discountTier = DiscountTier::findOrFail($id);

        $request->validate([
            'min_quantity' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $discountTier->update($request->all());

        return redirect()->route('admin.discount-tiers.index', $discountTier->product_id)
                         ->with('success', 'Discount tier updated successfully.');
    }

    public function destroy($id)
    {
        $discountTier = DiscountTier::findOrFail($id);
        $discountTier->delete();

        return redirect()->back()->with('success', 'Discount tier deleted successfully.');
    }
}
