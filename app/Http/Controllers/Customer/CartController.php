<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\DiscountTier;
use App\Models\CustomForm; // 🔥 don't forget to import CustomForm model

class CartController extends Controller
{
    // Add product to Cart (AJAX)
    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $approval = ProductApproval::where('product_id', $product->id)->first();

        $quantity = max((int) $request->quantity, $approval->minimum_quantity);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'product_name' => $product->product_name,
                'price' => $approval->custom_price,
                'quantity' => $quantity,
                'minimum_quantity' => $approval->minimum_quantity,
                'discount_percentage' => 0,
                'is_custom_form' => $approval->is_custom_form,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Product added to cart',
            'cart_count' => count($cart),
        ]);
    }

    // Update quantity in Cart (AJAX)
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;

            // Check Discount Tier if quantity updated
            $discount = DiscountTier::where('product_id', $request->product_id)
                ->where('min_quantity', '<=', $request->quantity)
                ->orderBy('min_quantity', 'desc')
                ->first();

            $cart[$request->product_id]['discount_percentage'] = $discount ? $discount->discount_percentage : 0;
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Cart updated',
            'cart' => $cart,
        ]);
    }

    // Remove product from Cart (NORMAL FORM POST)
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.view')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    // View Cart Page (LOAD custom forms if needed)
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $productForms = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if ($product && $product->approval && $product->approval->is_custom_form) {
                $fields = CustomForm::where('product_id', $productId)->get();
                $productForms[$productId] = $fields;
            }
        }

        return view('customer.cart', compact('cart', 'productForms'));
    }

    public function view()
    {
        $cart = session()->get('cart', []);
        $productForms = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->approval && $product->approval->is_custom_form) {
                $fields = CustomForm::where('product_id', $productId)->get();
                $productForms[$productId] = $fields;
            }
        }

        return view('customer.checkout', compact('cart', 'productForms'));
    }

}
