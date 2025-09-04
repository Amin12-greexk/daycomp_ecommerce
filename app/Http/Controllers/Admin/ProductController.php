<?php

// #####################################################################
// THIS IS THE SIMPLIFIED CONTROLLER FOR YOUR E-COMMERCE PROJECT
// File: app/Http/Controllers/Admin/ProductController.php
// #####################################################################

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // We only need the Product model now

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::latest()->paginate(20); // Show more items for a table view
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sale_price' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|integer|min:1',
            'short_description' => 'nullable|string|max:255',
            'is_custom_form' => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Approve a product to be shown on the storefront.
     */
    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->is_approved = true;
        $product->save();
        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    /**
     * Unapprove a product to hide it from the storefront.
     */
    public function unapprove($id)
    {
        $product = Product::findOrFail($id);
        $product->is_approved = false;
        $product->save();
        return redirect()->back()->with('success', 'Product unapproved successfully.');
    }

    /**
     * ** NEW METHOD **: Enable or disable the custom form for a product.
     */
    public function toggleCustomForm(Request $request, $id)
    {
        $request->validate([
            'is_custom_form' => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->is_custom_form = $request->is_custom_form;
        $product->save();

        return redirect()->back()->with('success', 'Custom form status updated.');
    }

    /**
     * Remove the specified product from the e-commerce database.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Product has been deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to delete product.');
        }
    }
}