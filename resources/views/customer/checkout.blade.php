@extends('layouts.customer')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-10 text-center text-slate-800 tracking-tight">Checkout Pesanan
        </h1>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow" role="alert">
                <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-xl p-6 sm:p-8">
            <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" id="checkoutForm"
                data-action="{{ route('checkout.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5"
                        placeholder="Masukkan nama lengkap Anda">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">No. Telepon <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5"
                        placeholder="Contoh: 081234567890">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5"
                        placeholder="email@example.com">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat
                        <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="4" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5"
                        placeholder="Masukkan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, dan kode pos">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-slate-700 mb-1.5">Metode Pembayaran
                        <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5 pr-10">
                        <option value="pay_on_place" {{ old('payment_method') == 'pay_on_place' ? 'selected' : '' }}>Bayar di
                            Tempat (COD)</option>
                        <option value="midtrans" {{ old('payment_method') == 'midtrans' ? 'selected' : '' }}>Pembayaran Online
                            (Midtrans)</option>
                    </select>
                </div>

                <div class="pt-4 text-right">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-semibold rounded-lg shadow-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-transform duration-150 hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                        Kirim Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- The Midtrans snap.js script and your custom script remain unchanged --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        const form = document.getElementById('checkoutForm');
        const paymentSelect = document.getElementById('payment_method');

        form.addEventListener('submit', async function (e) {
            if (paymentSelect.value === 'midtrans') {
                e.preventDefault();

                // Show a loading indicator (optional)
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            `;

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.dataset.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ensure CSRF token meta tag exists in layouts.customer
                        }
                    });

                    const contentType = response.headers.get("content-type");
                    const raw = await response.text();
                    console.log("Raw Response:", raw);

                    let result;
                    try {
                        result = JSON.parse(raw);
                    } catch (jsonError) {
                        console.error("JSON Parse Error:", jsonError);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal memproses respons dari server. Silakan coba lagi.',
                            confirmButtonColor: '#0ea5e9'
                        });
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                        return;
                    }

                    if (result.snap_token && result.order_id) {
                        console.log("Snap Token:", result.snap_token);
                        snap.pay(result.snap_token, {
                            onSuccess: (midtransResult) => {
                                console.log('Midtrans Success:', midtransResult);
                                window.location.href = `/thank-you/${result.order_id}`;
                            },
                            onPending: (midtransResult) => {
                                console.log('Midtrans Pending:', midtransResult);
                                window.location.href = `/thank-you/${result.order_id}`;
                            },
                            onError: (midtransError) => {
                                console.error("Snap Error:", midtransError);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: midtransError.message || 'Terjadi kesalahan saat proses pembayaran.',
                                    confirmButtonColor: '#0ea5e9'
                                });
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;
                            },
                            onClose: () => {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Dibatalkan',
                                    text: 'Anda menutup popup tanpa menyelesaikan pembayaran.',
                                    confirmButtonColor: '#0ea5e9'
                                });
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalButtonText;
                            }
                        });
                    } else {
                        console.warn("Snap token tidak tersedia:", result);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal Memulai Pembayaran',
                            text: result.error || 'Tidak dapat memulai sesi pembayaran online. Silakan periksa kembali pesanan Anda atau pilih metode pembayaran lain.',
                            confirmButtonColor: '#0ea5e9'
                        });
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }

                } catch (err) {
                    console.error("Fetch error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Tidak dapat mengirim pesanan Anda saat ini. Silakan coba beberapa saat lagi.',
                        confirmButtonColor: '#0ea5e9'
                    });
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            } else {
                // If not 'midtrans', let the form submit normally
                // You might want to add a loading state here too for COD
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            `;
            }
        });
    </script>
@endsection