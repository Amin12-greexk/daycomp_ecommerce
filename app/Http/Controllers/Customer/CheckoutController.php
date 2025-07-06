<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;



class CheckoutController extends Controller
{
    public function view()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        $productForms = [];
        $prefilledData = session('custom_form', []); // data yang disimpan dari modal

        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::with('approval')->find($productId);
            if ($product && $product->approval && $product->approval->is_custom_form) {
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

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|in:pay_on_place,midtrans',
        ]);
        \Log::info('Metode Pembayaran Dikirim:', ['dari_form' => $request->payment_method]);

        $cart = session()->get('cart', []);
        $customForm = session()->get('custom_form', []);
        Log::debug("Custom form final yang disimpan ke session:", $customForm); // ambil dari session

        if (empty($cart)) {
            return $request->ajax()
                ? response()->json(['error' => 'Keranjang kosong'], 400)
                : redirect()->route('customer.home')->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();
        try {
            $resi_code = 'DCMP-INV' . strtoupper(Str::random(10));

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
                'email' => $request->email,
            ]);

            foreach ($cart as $productId => $item) {
                $customFormInput = $customForm[$productId] ?? [];

                \Log::debug("Custom Form Data untuk Product ID $productId:", $customFormInput);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount_percentage'] ?? 0,
                    'custom_form_data' => $customFormInput,
                ]);

                // Kurangi stok
                $affected = 0;
                try {
                    $affected = DB::table('stocks')
                        ->where('product_id', $productId)
                        ->where('quantity', '>=', $item['quantity'])
                        ->orderBy('created_at')
                        ->limit(1)
                        ->decrement('quantity', $item['quantity']);
                } catch (\Exception $e) {
                    \Log::error("Error saat mengurangi stok untuk product_id: $productId. Pesan: " . $e->getMessage());
                    if ($affected === 0) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Stok tidak mencukupi atau tidak ditemukan.');
                    }
                }
            }

            // Midtrans
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
                session()->forget('custom_form');

                return $request->ajax()
                    ? response()->json([
                        'snap_token' => $order->snap_token,
                        'order_id' => $order->id
                    ])
                    : redirect()->route('thankyou', $order->id);
            }

            // COD (cash)
            DB::commit();
            session()->forget('cart');
            session()->forget('custom_form');

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

    public function cacheForm(Request $request)
    {
        Log::debug('💡 $_FILES:', $_FILES);

        $customForm = [];

        $formData = $request->input('custom_form', []);
        $fileData = $_FILES['custom_form'] ?? [];

        // Gabungkan key dari file agar loop tetap jalan
        foreach ($fileData['name'] ?? [] as $productId => $fileFields) {
            foreach ($fileFields as $fieldId => $fileName) {
                // Inject dummy value agar field file masuk loop
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

                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $fileArray['tmp_name'],
                        $fileArray['name'],
                        $fileArray['type'],
                        $fileArray['error'],
                        true
                    );

                    $imageName = $uploadedFile->getClientOriginalName();
                    $mimeType = $uploadedFile->getMimeType();

                    $sanityProjectId = env('SANITY_PROJECT_ID');
                    $sanityDataset = env('SANITY_DATASET');
                    $sanityToken = env('SANITY_API_TOKEN');

                    $uploadUrl = "https://{$sanityProjectId}.api.sanity.io/v2021-06-07/assets/images/{$sanityDataset}?filename=" . urlencode($imageName);

                    try {
                        $response = Http::withToken($sanityToken)
                            ->withHeaders(['Content-Type' => $mimeType])
                            ->send('POST', $uploadUrl, [
                                'body' => file_get_contents($uploadedFile->getRealPath())
                            ]);

                        if ($response->successful()) {
                            $url = $response->json()['document']['url'];
                            $customForm[$productId][$fieldId] = $url;
                            Log::debug("✅ Sanity upload sukses untuk field $fieldId", ['url' => $url]);
                        } else {
                            Log::error("❌ Gagal upload ke Sanity", ['body' => $response->body()]);
                            $customForm[$productId][$fieldId] = null;
                        }
                    } catch (\Exception $e) {
                        Log::error("❌ Exception Sanity: " . $e->getMessage());
                        $customForm[$productId][$fieldId] = null;
                    }
                } else {
                    $customForm[$productId][$fieldId] = $value;
                    Log::debug("Field $productId-$fieldId dianggap input biasa: ", [
                        'value' => $value
                    ]);
                }
            }
        }

        session(['custom_form' => $customForm]);

        return redirect()->route('checkout.view');
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
}
