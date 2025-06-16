
@extends('layouts.customer')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
    <div class="bg-white shadow-xl rounded-xl p-6 sm:p-10 text-center">
        <div class="mb-8">
            <svg class="mx-auto h-20 w-20 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="mt-6 text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">Terima Kasih Atas Pesanan Anda!</h1>
            <p class="mt-3 text-base text-slate-600">Pesanan Anda telah berhasil kami terima dan sedang diproses.</p>
            <p class="mt-2 text-lg font-semibold text-slate-700">
                Nomor Resi Pesanan Anda: <span class="text-sky-600">{{ $order->resi_code }}</span>
            </p>
        </div>

        <hr class="my-8 border-slate-200">

        <div class="space-y-6 text-left text-sm sm:text-base">
            <div>
                <h2 class="text-xl font-semibold text-slate-700 mb-3">Informasi Pelanggan</h2>
                <div class="space-y-1.5 text-slate-600">
                    <p><strong class="font-medium text-slate-500">Nama:</strong> {{ $order->name }}</p>
                    <p><strong class="font-medium text-slate-500">Nomor Telepon:</strong> {{ $order->phone }}</p>
                    <p><strong class="font-medium text-slate-500">Alamat </strong> {{ $order->address }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-slate-700 mt-6 mb-3">Ringkasan Pesanan</h2>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm align-middle">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-left font-semibold">Produk</th>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-center font-semibold">Jumlah</th>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-right font-semibold">Harga Satuan</th>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($order->orderDetails as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="py-3 px-4 sm:px-6 text-slate-700 font-medium">{{ $item->product->product_name ?? 'Produk Telah Dihapus' }}</td>
                                    <td class="py-3 px-4 sm:px-6 text-slate-600 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 px-4 sm:px-6 text-slate-600 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 sm:px-6 text-slate-800 font-medium text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                         <tfoot class="bg-slate-50 text-slate-700 font-semibold">
                            <tr>
                                <td colspan="3" class="py-3 px-4 sm:px-6 text-right text-base">Total Pembayaran:</td>
                                <td class="py-3 px-4 sm:px-6 text-right text-lg text-sky-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-6 space-y-1.5 text-slate-600">
                <p><strong class="font-medium text-slate-500">Metode Pembayaran:</strong>
    @if ($order->payment_method == 'midtrans')
        Pembayaran Online (Midtrans)
    @elseif ($order->payment_method == 'pay_on_place')
        Bayar di Tempat (COD)
    @else
        Metode Tidak Dikenali
    @endif
</p>
                <p><strong class="font-medium text-slate-500">Status Pesanan:</strong> 
                    <span class="font-semibold capitalize 
                        @switch($order->status)
                            @case('pending') text-yellow-600 @break
                            @case('paid') text-green-600 @break
                            @case('processing') text-blue-600 @break
                            @case('shipped') text-indigo-600 @break
                            @case('completed') text-green-700 @break
                            @case('cancelled') text-red-600 @break
                            @default text-slate-600 @break
                        @endswitch
                    ">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </p>
            </div>
        </div>

        <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('orders.downloadResi', $order->id) }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-semibold rounded-lg shadow-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-transform duration-150 hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download Resi
            </a>
            <a href="{{ route('customer.home') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-slate-300 text-base font-medium rounded-lg shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                Kembali ke Beranda
            </a>
        </div>
         @if ($order->payment_method === 'midtrans' && $order->status === 'pending' && $order->snap_token)
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-600 mb-2">Jika Anda belum menyelesaikan pembayaran:</p>
                <button id="pay-now-button"
                   class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-150">
                    Lanjutkan Pembayaran Sekarang
                </button>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    @if ($order->payment_method === 'midtrans' && $order->status === 'pending' && $order->snap_token)
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const payButton = document.getElementById('pay-now-button');
                if (payButton) {
                    payButton.addEventListener('click', function() {
                        window.snap.pay("{{ $order->snap_token }}", {
                            onSuccess: function (result) {
                                console.log('Midtrans Success:', result);
                                window.location.href = "{{ route('thankyou', $order->id) }}?status=success&order_id=" + result.order_id + "&transaction_status=" + result.transaction_status;
                            },
                            onPending: function (result) {
                                console.log('Midtrans Pending:', result);
                                window.location.href = "{{ route('thankyou', $order->id) }}?status=pending&order_id=" + result.order_id + "&transaction_status=" + result.transaction_status;
                            },
                            onError: function (result) {
                                console.error('Midtrans Error:', result);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: result.status_message || 'Terjadi kesalahan saat proses pembayaran.',
                                    confirmButtonColor: '#0ea5e9'
                                });
                            },
                            onClose: function () {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Ditutup',
                                    text: 'Anda menutup popup tanpa menyelesaikan pembayaran.',
                                    confirmButtonColor: '#0ea5e9'
                                });
                            }
                        });
                    });
                }
            });
        </script>
    @endif
@endsection
