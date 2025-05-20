<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductApproval;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['approval', 'stocks'])->get();

        // Tambahkan total_stock ke setiap produk
        foreach ($products as $product) {
            $product->total_stock = $product->stocks->sum('quantity');
        }

        return view('admin.products.index', compact('products'));
    }

    public function approve($id)
    {
        $approval = ProductApproval::findOrFail($id);
        $approval->is_approved = 1;
        $approval->save();

        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    public function unapprove($id)
    {
        $approval = ProductApproval::findOrFail($id);
        $approval->is_approved = 0;
        $approval->save();

        return redirect()->back()->with('success', 'Product unapproved successfully.');
    }

    public function approval()
    {
        return $this->hasOne(ProductApproval::class, 'product_id');
    }

    public function createApproval($id)
    {
        $product = Product::findOrFail($id);

        // Check if already exists
        $existing = ProductApproval::where('product_id', $id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Product already exists in approval list.');
        }

        ProductApproval::create([
            'product_id' => $id,
            'is_approved' => 0,
            'is_custom_form' => 0,
            'custom_price' => $product->product_price, // optional
        ]);

        return redirect()->back()->with('success', 'Product added to approval list.');
    }

    public function updatePrice(Request $request, $approval_id)
    {
        $request->validate([
            'custom_price' => 'required|numeric|min:0',
        ]);

        $approval = ProductApproval::findOrFail($approval_id);

        $approval->custom_price = $request->custom_price;
        $approval->save();

        return redirect()->back()->with('success', 'Custom price updated successfully.');
    }

    public function toggleCustomForm($approval_id)
    {
        $approval = \App\Models\ProductApproval::findOrFail($approval_id);

        $approval->is_custom_form = !$approval->is_custom_form; // toggle 0 to 1 or 1 to 0
        $approval->save();

        return redirect()->back()->with('success', 'Custom form status updated.');
    }

    public function updateShortDescription(Request $request, $id)
    {
        $request->validate([
            'short_description' => 'required|string|max:255',
        ]);

        $approval = \App\Models\ProductApproval::findOrFail($id);

        $approval->update([
            'short_description' => $request->short_description,
        ]);

        return redirect()->back()->with('success', 'Short Description updated successfully.');
    }

    public function updateMinimumQuantity(Request $request, $id)
    {
        $request->validate([
            'minimum_quantity' => 'required|integer|min:1',
        ]);

        $approval = ProductApproval::findOrFail($id);
        $approval->minimum_quantity = $request->minimum_quantity;
        $approval->save();

        return redirect()->back()->with('success', 'Minimum quantity updated.');
    }



}
