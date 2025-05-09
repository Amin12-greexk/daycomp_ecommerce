@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8 text-center">Keranjang Belanja</h1>

    @if (count($cart) > 0)
        <div class="overflow-x-auto bg-white p-6 rounded-lg shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left">Nama Produk</th>
                        <th class="py-3 px-4 text-left">Harga</th>
                        <th class="py-3 px-4 text-left">Jumlah</th>
                        <th class="py-3 px-4 text-left">Subtotal</th>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach ($cart as $productId => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $discount = $item['discount_percentage'] ?? 0;
                            $subtotal -= ($subtotal * $discount) / 100;
                            $grandTotal += $subtotal;
                        @endphp
                        <tr class="border-t">
                            <td class="py-3 px-4">{{ $item['product_name'] }}</td>
                            <td class="py-3 px-4">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <input type="number" 
       value="{{ $item['quantity'] }}" 
       min="{{ $item['minimum_quantity'] ?? 1 }}"
       class="border rounded w-20 text-center px-2 py-1 quantity-input"
       data-product-id="{{ $productId }}"
       data-minimum="{{ $item['minimum_quantity'] ?? 1 }}">

                            </td>
                            <td class="py-3 px-4">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mt-6 text-lg font-bold">
                <div>Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>

            <div class="flex justify-end mt-4">
                <button id="checkout-button" 
                        class="bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded">
                    Checkout
                </button>
            </div>
        </div>
    @else
        <div class="text-center text-gray-600">
            Keranjang kosong. <a href="{{ route('customer.home') }}" class="text-blue-500 hover:underline">Belanja sekarang</a>.
        </div>
    @endif
</div>

<!-- Modal -->
<div id="checkout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center">Formulir Pemesanan</h2>

        <form id="custom-form" method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
            @csrf
            @foreach ($cart as $productId => $item)
                @if (isset($productForms[$productId]))
                    <h3 class="text-lg font-bold mt-4">{{ $item['product_name'] }}</h3>
                    @foreach ($productForms[$productId] as $field)
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">{{ $field->field_label }}</label>
                            @if ($field->field_type == 'text')
                                <input type="text" name="custom_form[{{ $productId }}][{{ $field->id }}]"
                                       class="w-full border px-3 py-2 rounded" {{ $field->is_required ? 'required' : '' }}>
                            @elseif ($field->field_type == 'date')
                                <input type="date" name="custom_form[{{ $productId }}][{{ $field->id }}]"
                                       class="w-full border px-3 py-2 rounded" {{ $field->is_required ? 'required' : '' }}>
                            @elseif ($field->field_type == 'file')
                                <input type="file" name="custom_form[{{ $productId }}][{{ $field->id }}]"
                                       class="w-full" {{ $field->is_required ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endforeach
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" id="cancel-button" 
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                    Batal
                </button>
                            <a href="{{ route('checkout.view') }}" 
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded inline-block text-center">
            Lanjutkan Pembayaran
            </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const checkoutButton = document.getElementById('checkout-button');
    const modal = document.getElementById('checkout-modal');
    const cancelButton = document.getElementById('cancel-button');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
    const productId = this.getAttribute('data-product-id');
    const newQuantity = parseInt(this.value);
    const minQuantity = parseInt(this.getAttribute('data-minimum'));

    if (newQuantity < minQuantity) {
        this.value = minQuantity;

        // 🔔 SweetAlert untuk minimal beli
        Swal.fire({
            icon: 'warning',
            title: 'Jumlah terlalu sedikit!',
            text: `Minimal pembelian untuk produk ini adalah ${minQuantity} pcs.`,
            confirmButtonText: 'OK'
        });

        return;
    }

    fetch('{{ route('cart.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Cart updated:', data);
        window.location.reload();
    })
    .catch(error => {
        console.error('Error updating cart:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat memperbarui jumlah.',
        });
    });
});

    });

    checkoutButton.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    cancelButton.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
});
</script>
@endsection