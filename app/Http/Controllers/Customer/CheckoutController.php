<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Illuminate\Support\Str;


class CheckoutController extends Controller
{
    public function view()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        return view('customer.checkout', [
            'cart' => $cart
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|in:pay_on_place,midtrans',
        ]);
        \Log::info('Metode Pembayaran Dikirim:', [
            'dari_form' => $request->payment_method
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return $request->ajax()
                ? response()->json(['error' => 'Keranjang kosong'], 400)
                : redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();
        try {
            $resi_code = 'INV-' . strtoupper(Str::random(10));

            $order = Order::create([
                'resi_code' => $resi_code,
                'total_price' => $this->calculateTotal($cart),
                'payment_method' => $request->payment_method,
                'snap_token' => null,
                'midtrans_transaction_id' => null,
                'payment_status' => 'pending',
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            foreach ($cart as $productId => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount_percentage'] ?? 0,
                    'custom_form_data' => $request->input("custom_form.$productId", []),
                ]);
            }

            if ($request->payment_method === 'midtrans') {
                $params = [
                    'transaction_details' => [
                        'order_id' => $order->resi_code,
                        'gross_amount' => $order->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $order->name ?? 'Guest',
                        'phone' => $order->phone ?? 'N/A',
                        'address' => $order->address ?? 'N/A',
                    ],
                    'item_details' => $this->formatItems($order),
                ];

                $snapToken = Snap::getSnapToken($params);
                $order->snap_token = is_array($snapToken) ? $snapToken[0] : (string) $snapToken;
                $order->save();

                DB::commit();
                session()->forget('cart');

                return $request->ajax()
                    ? response()->json([
                        'snap_token' => $order->snap_token,
                        'order_id' => $order->id
                    ])
                    : redirect()->route('thankyou', $order->id);
            }

            // COD (pay on place)
            DB::commit();
            session()->forget('cart');

            return redirect()->route('thankyou', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());

            return $request->ajax()
                ? response()->json([
                    'error' => 'Gagal checkout',
                    'message' => $e->getMessage()
                ], 500)
                : back()->with('error', 'Gagal checkout: ' . $e->getMessage());
        }
    }



    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $discount = $item['discount_percentage'] ?? 0;
            $subtotal -= ($subtotal * $discount) / 100;
            $total += $subtotal;
        }
        return $total;
    }

    private function formatItems($order)
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->product_name ?? 'Produk'
            ];
        }
        return $items;
    }
}
