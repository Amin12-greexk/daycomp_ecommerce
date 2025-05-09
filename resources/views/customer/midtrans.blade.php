@extends('layouts.customer')

@section('content')
    <div class="text-center py-16">
        <h1 class="text-2xl font-bold text-gray-700 mb-4">Sedang mengarahkan ke pembayaran...</h1>
        <p>Jangan tutup halaman ini.</p>
    </div>
@endsection

@section('scripts')
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        window.onload = function () {
            snap.pay('{{ $snap_token }}', {
                onSuccess: function () {
                    window.location.href = "/thank-you/{{ $order_id }}";
                },
                onPending: function () {
                    window.location.href = "/thank-you/{{ $order_id }}";
                },
                onError: function () {
                    alert("Terjadi kesalahan saat memproses pembayaran.");
                }
            });
        };
    </script>
@endsection