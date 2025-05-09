@extends('layouts.customer')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow rounded-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-4 text-green-600">Terima Kasih!</h1>
            <p class="text-gray-700">Pesanan Anda telah diterima.</p>
            <p class="text-gray-700 font-bold mt-2">Resi: {{ $order->resi_code }}</p>
        </div>

        <hr class="my-8">

        <!-- Order Information -->
        <div class="space-y-4 text-gray-700">
            <h2 class="text-xl font-semibold mb-2">Informasi Pelanggan</h2>
            <p><strong>Nama:</strong> {{ $order->name }}</p>
            <p><strong>Nomor Telepon:</strong> {{ $order->phone }}</p>
            <p><strong>Alamat:</strong> {{ $order->address }}</p>

            <h2 class="text-xl font-semibold mt-8 mb-2">Ringkasan Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Produk</th>
                            <th class="py-3 px-4 text-left">Jumlah</th>
                            <th class="py-3 px-4 text-left">Harga</th>
                            <th class="py-3 px-4 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $item)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $item->product->product_name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $item->quantity }}</td>
                                <td class="py-2 px-4">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-2 px-4">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-8 text-lg font-bold">
                <div>Total Bayar:</div>
                <div>Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
            </div>

            <p class="mt-4"><strong>Metode Pembayaran:</strong>
                {{ $order->payment_method == 'midtrans' ? 'Bayar Online (Midtrans)' : 'Bayar di Tempat' }}
            </p>
            <p><strong>Status:</strong> Menunggu Pembayaran</p>
        </div>

        <!-- Download / Print Button -->
        <div class="flex justify-center mt-8">
            <a href="{{ route('orders.downloadResi', $order->id) }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded font-bold transition-all">
                Download Resi
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    @if ($order->payment_method === 'midtrans')
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script>
            window.snap.pay("{{ $order->snap_token }}", {
                onSuccess: function (result) {
                    window.location.href = "{{ route('thankyou', $order->id) }}";
                }
            });
        </script>
    @endif
@endsection