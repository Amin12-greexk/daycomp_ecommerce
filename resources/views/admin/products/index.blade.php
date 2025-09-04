@extends('layouts.admin')

@section('content')
    {{-- Font Awesome is used for icons. Ensure it's loaded in your main layout. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800 flex items-center">
                <i class="fas fa-boxes-stacked mr-3 text-slate-500"></i>
                Kelola Produk Ecommerce
            </h1>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm flex items-center"
                role="alert">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                <div>
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm align-middle">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-6 text-left font-semibold">Produk</th>
                            <th scope="col" class="py-3.5 px-6 text-left font-semibold">Harga Jual</th>
                            <th scope="col" class="py-3.5 px-6 text-center font-semibold">Status</th>
                            <th scope="col" class="py-3.5 px-6 text-center font-semibold">Form Kustom</th>
                            <th scope="col" class="py-3.5 px-6 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($products as $product)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img class="h-12 w-12 rounded-lg object-cover shadow-sm"
                                                src="{{ $product->image_url ?? asset('placeholder.jpg') }}"
                                                alt="{{ $product->product_name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-semibold text-slate-900">{{ $product->product_name }}</div>
                                            <div class="text-xs text-slate-500">Kode: {{ $product->product_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-slate-800">Rp {{ number_format($product->sale_price, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $product->is_approved ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $product->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <!-- Improved Toggle Switch -->
                                    <form action="{{ route('admin.products.toggleCustomForm', $product->id) }}" method="POST">
                                        @csrf
                                        <select name="is_custom_form" onchange="this.form.submit()"
                                            class="text-xs font-semibold rounded-full border-transparent focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 py-1 px-3 {{ $product->is_custom_form ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-800' }}">
                                            <option value="1" {{ $product->is_custom_form ? 'selected' : '' }}>On
                                            </option>
                                            <option value="0" {{ !$product->is_custom_form ? 'selected' : '' }}>Off
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->stock_quantity < $product->minimum_quantity)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Stok Rendah ({{ $product->stock_quantity }})
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Cukup ({{ $product->stock_quantity }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-4 text-slate-500">
                                        <!-- Approve/Unapprove Buttons -->
                                        @if ($product->is_approved)
                                            <form action="{{ route('admin.products.unapprove', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="hover:text-red-600" title="Unapprove">
                                                    <i class="fas fa-times-circle fa-fw fa-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="hover:text-green-600" title="Approve">
                                                    <i class="fas fa-check-circle fa-fw fa-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="hover:text-indigo-600"
                                            title="Edit Produk">
                                            <i class="fas fa-pencil-alt fa-fw fa-lg"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                            onsubmit="return confirm('Anda yakin ingin menghapus produk ini dari toko?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="hover:text-red-600" title="Hapus Produk">
                                                <i class="fas fa-trash-alt fa-fw fa-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16 px-6">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open fa-3x text-slate-400 mb-4"></i>
                                        <h3 class="text-lg font-medium text-slate-800">Belum ada produk</h3>
                                        <p class="mt-1 text-sm text-slate-500">Jalankan sinkronisasi untuk mengambil produk
                                            dari warehouse.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="mt-8">
                {{-- This will render the styled pagination links from your AppServiceProvider --}}
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection