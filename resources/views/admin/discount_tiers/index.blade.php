@extends('layouts.admin')

@section('content')
    <div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Kelola Tingkatan Diskon</h1>
                <p class="text-sm text-slate-600 mt-1">Untuk Produk: <span
                        class="font-semibold text-sky-700">{{ $product->product_name }}</span></p>
            </div>
            <a href="{{ route('admin.discount-tiers.create', $product->id) }}"
                class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Tingkatan Diskon Baru
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

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm align-middle">
                    <thead class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Kuantitas Minimum</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Persentase Diskon (%)</th>
                            <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($discountTiers as $tier)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="py-4 px-4 sm:px-6 text-slate-700 font-medium whitespace-nowrap">
                                    {{ $tier->min_quantity }}</td>
                                <td class="py-4 px-4 sm:px-6 text-slate-600 whitespace-nowrap">{{ $tier->discount_percentage }}%
                                </td>
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.discount-tiers.edit', $tier->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-150">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.discount-tiers.destroy', $tier->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus tingkatan diskon ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-12 px-4 sm:px-6">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.84 6.988 6 7.655 6 8.484v.058c0 .849.684 1.513 1.502 1.513h1.098a2.25 2.25 0 012.25 2.25v.059c0 .576-.224 1.095-.632 1.498l-.547.456a2.25 2.25 0 00-.632 1.498v.058c0 .849.684 1.513 1.502 1.513h.282a2.25 2.25 0 012.25 2.25v.059c0 .576-.224 1.095-.632 1.498l-.547.456a2.25 2.25 0 00-.632 1.498v.058c0 .849.684 1.513 1.502 1.513h.282a2.25 2.25 0 012.25 2.25v.059c0 .576-.224 1.095-.632 1.498l-.547.456a2.25 2.25 0 00-.632 1.498v.058c0 .849.684 1.513 1.502 1.513h.282a2.25 2.25 0 012.25 2.25v.059c0 .849-.684 1.513-1.502 1.513h-1.098a2.25 2.25 0 01-2.25-2.25v-.059c0-.576.224-1.095.632-1.498l.547-.456a2.25 2.25 0 00.632-1.498v-.058c0-.849-.684-1.513-1.502-1.513H12a2.25 2.25 0 01-2.25-2.25v-.059c0-.576.224-1.095.632-1.498l.547-.456a2.25 2.25 0 00.632-1.498v-.058c0-.849-.684-1.513-1.502-1.513H9.75M12 8.25c1.355 0 2.697.056 4.024.166C17.16 6.988 18 7.655 18 8.484v.058c0 .849-.684 1.513-1.502 1.513h-1.098a2.25 2.25 0 01-2.25-2.25v-.059c0-.576.224-1.095.632-1.498l.547-.456a2.25 2.25 0 00.632-1.498v-.058c0-.849-.684-1.513-1.502-1.513H12z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada tingkatan diskon</h3>
                                        <p class="mt-1 text-sm text-slate-500">Silakan tambahkan tingkatan diskon baru untuk
                                            produk ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection