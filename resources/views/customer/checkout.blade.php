@extends('layouts.customer')

@section('content')
    <div class="max-w-4xl mx-auto p-8 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-6 text-center">Checkout</h1>

        <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" id="checkoutForm">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">No. Telepon</label>
                <input type="text" name="phone" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Alamat Lengkap</label>
                <textarea name="address" required class="w-full border px-3 py-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="pay_on_place">Bayar di Tempat</option>
                    <option value="midtrans">Bayar Online (Midtrans)</option>
                </select>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                    Kirim Pesanan
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        const form = document.getElementById('checkoutForm');
        const paymentSelect = document.getElementById('payment_method');

        form.addEventListener('submit', function (e) {
            // ✅ Jalankan Snap hanya jika metode pembayaran adalah midtrans
            if (paymentSelect.value !== 'midtrans') return;

            e.preventDefault();

            fetch("{{ route('checkout.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: form.name.value,
                    phone: form.phone.value,
                    address: form.address.value,
                    payment_method: paymentSelect.value,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: function (result) {
                                window.location.href = `/thankyou/success/${data.order_id}`;
                            },
                            onPending: function (result) {
                                window.location.href = `/thankyou/success/${data.order_id}`;
                            },
                            onError: function (result) {
                                alert("Pembayaran gagal. Silakan coba lagi.");
                            }
                        });
                    } else {
                        alert("Gagal mendapatkan token pembayaran.");
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Terjadi kesalahan.");
                });
        });
    </script>
@endsection