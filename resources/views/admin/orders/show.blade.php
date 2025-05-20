@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-2">
        Detail Pesanan - <span class="text-blue-600">{{ $order->resi_code }}</span>
    </h1>
    

    <div class="bg-white shadow-xl rounded-lg p-6 space-y-8">
        <!-- Informasi Pelanggan -->
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Pelanggan</h3>
            <ul class="text-gray-600 space-y-1">
                <li><strong>Nama:</strong> {{ $order->name }}</li>
                <li><strong>Telepon:</strong> {{ $order->phone }}</li>
                <li><strong>Alamat:</strong> {{ $order->address }}</li>
            </ul>
        </div>

        <!-- Produk Dipesan -->
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Produk Dipesan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm text-gray-700">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left border">Produk</th>
                            <th class="py-3 px-4 text-left border">Jumlah</th>
                            <th class="py-3 px-4 text-left border">Harga</th>
                            <th class="py-3 px-4 text-left border">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border">{{ $item->product->product_name ?? 'Produk Dihapus' }}</td>
                                <td class="py-3 px-4 border">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 border">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 border">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Formulir Kustom -->
        @foreach ($order->orderDetails as $item)
            @if ($item->custom_form_data && is_array($item->custom_form_data) && count($item->custom_form_data) > 0)
                <div class="bg-gray-50 border rounded p-4">
                    <h4 class="font-bold text-gray-800 mb-2">Formulir Kustom - {{ $item->product->product_name ?? 'Produk' }}</h4>
                    <ul class="text-gray-700 text-sm space-y-1">
                        @foreach ($item->custom_form_data as $fieldId => $value)
                            @php
                                $field = \App\Models\CustomForm::find($fieldId);
                            @endphp
                            <li>
                                <strong>{{ $field->field_label ?? 'Field #' . $fieldId }}:</strong>
                                @if ($field && $field->field_type === 'file')
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $value }}" alt="Preview" class="w-24 h-24 object-cover rounded border">
                                        <a href="{{ $value }}" target="_blank" class="text-blue-600 underline">Lihat Gambar Penuh</a>
                                    </div>
                                @else
                                    {{ $value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach

        <!-- Status Pemesanan -->
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Status Pemesanan</h3>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-4">
                @csrf
                <select name="status"
                    class="border border-gray-300 px-4 py-2 rounded-md text-gray-700 focus:outline-none focus:ring focus:border-blue-300">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Dibayar</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-2 rounded-md shadow-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
