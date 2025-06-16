@extends('layouts.admin')

@section('content')
<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Daftar Pesanan</h1>
        <!-- 
        <a href="#" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:border-sky-800 focus:ring focus:ring-sky-300 disabled:opacity-25 transition">
            Buat Pesanan Baru
        </a>
        -->
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

    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-grow w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama customer atau resi..."
                       class="block w-full border-slate-300 rounded-lg shadow-sm pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
            </div>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm align-middle">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Kode Resi</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Nama Customer</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Status</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Total Harga</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4 px-4 sm:px-6 font-semibold text-sky-700 whitespace-nowrap">{{ $order->resi_code }}</td>
                            <td class="py-4 px-4 sm:px-6 text-slate-700 whitespace-nowrap">{{ $order->name }}</td>
                            <td class="py-4 px-4 sm:px-6 capitalize whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($order->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('processing') bg-blue-100 text-blue-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('shipped') bg-indigo-100 text-indigo-800 @break
                                        @case('cancelled') bg-red-100 text-red-800 @break
                                        @case('paid') bg-teal-100 text-teal-800 @break
                                        @default bg-slate-100 text-slate-800 @break
                                    @endswitch
                                ">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-slate-600 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="inline-flex items-center px-3.5 py-2 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 px-4 sm:px-6">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada pesanan ditemukan</h3>
                                    <p class="mt-1 text-sm text-slate-500">Coba ubah filter pencarian Anda atau tunggu pesanan baru masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders instanceof \Illuminate\Pagination\AbstractPaginator && $orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }} </div>
        @endif
    </div>
@endsection
