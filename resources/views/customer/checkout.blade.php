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
                <select name="payment_method" required class="w-full border px-3 py-2 rounded" id="payment_method">
                    <option value="pay_on_place">Bayar di Tempat</option>
                    <option value="midtrans">Midtrans</option>
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

        form.addEventListener('submit', async function (e) {
            if (paymentSelect.value === 'midtrans') {
                e.preventDefault();

                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    const result = await response.json();

                    if (result.snap_token && result.order_id) {
                        snap.pay(result.snap_token, {
                            onSuccess: () => window.location.href = `/thank-you/${result.order_id}`,
                            onPending: () => window.location.href = `/thank-you/${result.order_id}`,
                            onError: () => alert('Pembayaran gagal.')
                        });
                    }
                    else {
                        alert(result.error || 'Gagal membuat Snap Token.');
                    }
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response received:", text);
                    alert("Gagal melakukan checkout. Silakan coba lagi.");
                }
            }
        });
    </script>
@endsection