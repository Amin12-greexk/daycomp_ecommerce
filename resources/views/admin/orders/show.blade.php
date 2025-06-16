@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">
                    Detail Pesanan
                </h1>
                <p class="text-sm text-slate-600 mt-1">Kode Resi: <span
                        class="font-semibold text-sky-600">{{ $order->resi_code }}</span></p>
            </div>
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 text-sm font-medium rounded-lg shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-slate-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Pesanan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">Oops!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-lg p-6 sm:p-8 space-y-10"> 
            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-200">Informasi Pelanggan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm"> 
                    <div>
                        <span class="font-medium text-slate-500 block mb-0.5">Nama:</span>
                        <p class="text-slate-700 font-medium">{{ $order->name }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-slate-500 block mb-0.5">Telepon:</span>
                        <p class="text-slate-700 font-medium">{{ $order->phone }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="font-medium text-slate-500 block mb-0.5">Alamat:</span>
                        <p class="text-slate-700 font-medium leading-relaxed">{{ $order->address }}</p>
                    </div>
                </div>
            </section>

            <!-- Produk Dipesan -->
            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-200">Produk Dipesan</h2>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm align-middle">
                        <thead class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Produk</th>
                                <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Jumlah</th>
                                <th scope="col" class="py-3.5 px-4 sm:px-6 text-right font-semibold">Harga Satuan</th>
                                <th scope="col" class="py-3.5 px-4 sm:px-6 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($order->orderDetails as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="py-4 px-4 sm:px-6 text-slate-700 font-medium">
                                        {{ $item->product->product_name ?? 'Produk Telah Dihapus' }}</td>
                                    <td class="py-4 px-4 sm:px-6 text-slate-600 text-center">{{ $item->quantity }}</td>
                                    <td class="py-4 px-4 sm:px-6 text-slate-600 text-right">Rp
                                        {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4 sm:px-6 text-slate-800 font-medium text-right">Rp
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 px-4 sm:px-6 text-slate-500">
                                        Tidak ada produk dalam pesanan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($order->orderDetails->isNotEmpty())
                            <tfoot class="bg-slate-100 text-slate-700 font-semibold">
                                <tr>
                                    <td colspan="3" class="py-3.5 px-4 sm:px-6 text-right text-base">Total Keseluruhan:</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right text-lg text-sky-700">Rp
                                        {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>

            <!-- Formulir Kustom -->
            @php 
                            $hasCustomForms = false;
                if ($order->orderDetails->isNotEmpty()) {
                    foreach ($order->orderDetails as $itemCheck) {
                        if ($itemCheck->custom_form_data && is_array($itemCheck->custom_form_data) && count($itemCheck->custom_form_data) > 0) {
                            $hasCustomForms = true;
                            break;
                        }
                    }
                }
            @endphp

            @if($hasCustomForms)
                <section>
                    <h2 class="text-xl font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-200">Detail Formulir Kustom
                    </h2>
                    <div class="space-y-6">
                        @foreach ($order->orderDetails as $item)
                            @if ($item->custom_form_data && is_array($item->custom_form_data) && count($item->custom_form_data) > 0)
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 sm:p-6">
                                    <h3 class="text-md font-semibold text-sky-700 mb-3">
                                        {{ $item->product->product_name ?? 'Produk' }}
                                        <span class="text-xs text-slate-500 font-normal">(Kuantitas: {{ $item->quantity }})</span>
                                    </h3>
                                    <ul class="text-sm space-y-3"> 
                                        @foreach ($item->custom_form_data as $fieldId => $value)
                                                    @php
                                                        $field = \App\Models\CustomForm::find($fieldId);
                                                    @endphp
                                                    <li class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-1 items-start">
                                                        <strong
                                                            class="text-slate-600 md:col-span-1">{{ $field->field_label ?? 'Field #' . $fieldId }}:</strong>
                                                        <div class="text-slate-700 md:col-span-2 break-words">
                                                            @if ($field && $field->field_type === 'file' && $value)
                                                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mt-1">
                                        
                                                                    <a href="{{ $value }}" target="_blank"
                                                                        class="block w-28 h-28 sm:w-24 sm:h-24 flex-shrink-0">
                                                                        <img src="{{ $value }}" alt="Preview File"
                                                                            class="w-full h-full object-cover rounded-md border border-slate-300 shadow-sm hover:opacity-80 transition-opacity">
                                                                    </a>
                                                                    <a href="{{ $value }}" target="_blank"
                                                                        class="text-sky-600 hover:text-sky-700 underline text-xs sm:text-sm mt-2 sm:mt-0">
                                                                        Lihat/Unduh File
                                                                    </a>
                                                                </div>
                                                            @else
                                                                {{ $value ?: '-' }}
                                                            @endif
                                                        </div>
                                                    </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Status Pemesanan -->
            <section>
                <h2 class="text-xl font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-200">Status Pemesanan</h2>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                    @csrf
                    {{-- @method('PATCH') <!-- Removed: Using POST as per route definition --> --}}
                    <div>
                        <label for="status" class="sr-only">Ubah Status</label>
                        <select name="status" id="status"
                            class="block w-full sm:w-auto border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm px-4 py-2.5 pr-10">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran
                            </option>
                            <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Dibayar</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Simpan Perubahan Status
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection