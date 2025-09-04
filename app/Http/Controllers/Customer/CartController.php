<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DiscountTier;
use App\Models\CustomForm;

class CartController extends Controller
{
    /**
     * Add a product to the cart via AJAX.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Ensure quantity meets the product's minimum requirement
        $quantity = max((int) $request->quantity, $product->minimum_quantity);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            // Get all data directly from the Product model
            $cart[$product->id] = [
                // THE FIX: Use 'product_name' to match your Blade view
                'product_name' => $product->product_name,
                'price' => $product->sale_price,
                'quantity' => $quantity,
                'minimum_quantity' => $product->minimum_quantity,
                'image' => $product->image_url,
                'is_custom_form' => $product->is_custom_form,
                'discount_percentage' => 0, // Default discount
            ];
        }

        session()->put('cart', $cart);

        // Return the JSON response the frontend script expects
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => count(session('cart'))
        ]);
    }

    /**
     * Update quantity in Cart via AJAX.
     */
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

    /**
     * Remove product from Cart.
     */
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.view')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Display the cart page.
     */
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $productForms = [];

        foreach ($cart as $productId => $item) {
            // Find the product in our local e-commerce database
            $product = Product::find($productId);

            // Check if the product exists and requires a custom form
            if ($product && $product->is_custom_form) {
                $productForms[$productId] = CustomForm::where('product_id', $productId)->orderBy('field_order')->get();
            }
        }

        // THE FIX: Use 'customer.cart' to match your likely view path
        return view('customer.cart', compact('cart', 'productForms'));
    }
}

