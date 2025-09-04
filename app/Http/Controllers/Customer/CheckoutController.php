<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function view()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        $productForms = [];
        $prefilledData = session('custom_form', []);

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_custom_form) {
                $fields = \App\Models\CustomForm::where('product_id', $productId)->get();
                foreach ($fields as $field) {
                    $field->value = $prefilledData[$productId][$field->id] ?? null;
                }
                $productForms[$productId] = $fields;
            }
        }
        return view('customer.checkout', [
            'cart' => $cart,
            'productForms' => $productForms,
        ]);
    }

    /**
     * Process the order, validate stock via API, and handle payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|in:pay_on_place,midtrans',
        ]);

        $cart = session()->get('cart', []);
        $customForm = session()->get('custom_form', []);

        if (empty($cart)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        // Real-time stock check via API before starting the transaction
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product)
                continue;

            $response = Http::withToken(env('WAREHOUSE_API_TOKEN'))
                ->acceptJson()
                ->get(env('WAREHOUSE_API_URL') . '/api/v1/stock/' . $product->product_code);

            if ($response->failed() || $response->json()['data']['quantity'] < $item['quantity']) {
                $realStock = $response->successful() ? $response->json()['data']['quantity'] : 'N/A';
                $errorMessage = "Stok untuk produk '{$product->product_name}' tidak mencukupi. Sisa stok: {$realStock}.";

                // THE FIX: Return JSON error for AJAX requests
                if ($request->wantsJson()) {
                    return response()->json(['message' => $errorMessage], 422); // 422 Unprocessable Entity
                }
                return redirect()->route('cart.view')->with('error', $errorMessage);
            }
        }

        DB::beginTransaction();
        try {
            $resi_code = 'DCMP-INV' . strtoupper(Str::random(10));

            $order = Order::create([
                'resi_code' => $resi_code,
                'total_price' => $this->calculateTotal($cart),
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if (!$product)
                    continue;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount_percentage'] ?? 0,
                    'custom_form_data' => json_encode($customForm[$productId] ?? []),
                ]);

                // Adjust stock in warehouse via API
                $stockResponse = Http::withToken(env('WAREHOUSE_API_TOKEN'))
                    ->acceptJson()
                    ->post(env('WAREHOUSE_API_URL') . '/api/v1/stock/adjust', [
                        'product_code' => $product->product_code,
                        'quantity_changed' => -(int) $item['quantity'],
                        'reason' => 'SALE_ORDER_' . $order->resi_code,
                    ]);

                if ($stockResponse->failed()) {
                    DB::rollBack();
                    Log::critical("CRITICAL: Stock adjustment failed for order {$order->resi_code} after validation passed.", ['response' => $stockResponse->body()]);
                    $errorMessage = 'Gagal memproses pesanan di gudang. Silakan hubungi support.';
                    // THE FIX: Return JSON error for AJAX requests
                    if ($request->wantsJson()) {
                        return response()->json(['message' => $errorMessage], 500);
                    }
                    return redirect()->back()->with('error', $errorMessage);
                }
            }

            // Midtrans payment logic
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
                session()->forget(['cart', 'custom_form']);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'snap_token' => $order->snap_token,
                        'order_id' => $order->id,
                        'redirect_url' => route('thankyou', $order->id)
                    ]);
                }
                return redirect()->route('thankyou', $order->id);
            }

            // COD (cash) payment logic
            DB::commit();
            session()->forget(['cart', 'custom_form']);

            return redirect()->route('thankyou', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Gagal checkout',
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }
            return back()->with('error', 'Gagal checkout: Terjadi kesalahan sistem.');
        }
    }

    /**
     * Caches custom form data in the session, including file uploads to Sanity.
     */
    public function cacheForm(Request $request)
    {
        $customForm = [];
        $formData = $request->input('custom_form', []);
        $fileData = $_FILES['custom_form'] ?? [];

        foreach ($fileData['name'] ?? [] as $productId => $fileFields) {
            foreach ($fileFields as $fieldId => $fileName) {
                $formData[$productId][$fieldId] = null;
            }
        }

        foreach ($formData as $productId => $fields) {
            foreach ($fields as $fieldId => $value) {
                $fieldModel = \App\Models\CustomForm::find($fieldId);
                $fieldType = $fieldModel?->field_type ?? 'text';

                if ($fieldType === 'file' && isset($fileData['name'][$productId][$fieldId]) && $fileData['name'][$productId][$fieldId] !== '') {
                    $fileArray = [
                        'name' => $fileData['name'][$productId][$fieldId],
                        'type' => $fileData['type'][$productId][$fieldId],
                        'tmp_name' => $fileData['tmp_name'][$productId][$fieldId],
                        'error' => $fileData['error'][$productId][$fieldId],
                        'size' => $fileData['size'][$productId][$fieldId],
                    ];
                    $uploadedFile = new UploadedFile($fileArray['tmp_name'], $fileArray['name'], $fileArray['type'], $fileArray['error'], true);

                    try {
                        $response = Http::withToken(env('SANITY_API_TOKEN'))
                            ->withHeaders(['Content-Type' => $uploadedFile->getMimeType()])
                            ->send('POST', "https://" . env('SANITY_PROJECT_ID') . ".api.sanity.io/v2021-06-07/assets/images/" . env('SANITY_DATASET'), [
                                'body' => file_get_contents($uploadedFile->getRealPath())
                            ]);

                        if ($response->successful()) {
                            $customForm[$productId][$fieldId] = $response->json()['document']['url'];
                        }
                    } catch (\Exception $e) {
                        Log::error("Sanity Upload Exception: " . $e->getMessage());
                    }
                } else {
                    $customForm[$productId][$fieldId] = $value;
                }
            }
        }
        session(['custom_form' => $customForm]);
        return redirect()->route('checkout.view');
    }

    /**
     * Helper function to calculate the total price of the cart.
     */
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

    /**
     * Helper function to format cart items for Midtrans.
     */
    private function formatItems($order)
    {
        $items = [];
        foreach ($order->fresh()->orderDetails as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->product_name ?? 'Produk'
            ];
        }
        return $items;
    }

    /**
     * Allows a customer to download their own order receipt.
     */
    public function downloadResi(Order $order)
    {
        $order->load('orderDetails.product');
        $pdf = Pdf::loadView('pdf.order_receipt', ['order' => $order]);
        $fileName = 'resi-' . $order->resi_code . '.pdf';
        return $pdf->stream($fileName);
    }
}
