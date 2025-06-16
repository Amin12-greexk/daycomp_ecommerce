@extends('layouts.admin')

@section('content')
    <div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Kelola Produk Ecommerce</h1>
            <!-- 
                <a href="#" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:border-sky-800 focus:ring focus:ring-sky-300 disabled:opacity-25 transition">
                    Tambah Produk Baru
                </a>
                -->
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-6 gap-y-8">
            @forelse ($products as $product)
                <div
                    class="bg-white shadow-lg rounded-xl flex flex-col transition-all duration-300 hover:shadow-2xl 
                                    {{ $product->approval && $product->total_stock < $product->approval->minimum_quantity ? 'ring-2 ring-red-400 border-red-400' : 'border border-transparent' }}">

                    <div class="p-5 flex-grow">
                        @if ($product->image_url)
                            <div class="mb-4">
                                <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                                    class="w-full h-48 object-cover rounded-lg shadow">
                            </div>
                        @else
                            <div class="mb-4 flex items-center justify-center w-full h-48 bg-slate-100 rounded-lg shadow">
                                <svg class="w-16 h-16 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        @endif

                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800 leading-tight">{{ $product->product_name }}</h2>
                                <p class="text-xs text-slate-500">ID: {{ $product->id }}</p>
                            </div>
                            @if ($product->approval)
                                <span
                                    class="text-xs font-semibold px-2.5 py-1 rounded-full
                                                {{ $product->approval->is_approved ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $product->approval->is_approved ? 'Approved' : 'Not Approved' }}
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                    Pending Review
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-slate-500 mb-0.5">Harga Beli: <span class="font-medium text-slate-600">Rp
                                {{ number_format($product->purchase_price, 0, ',', '.') }}</span></p>
                        <p class="text-sm text-slate-500">Stok: <span
                                class="font-semibold text-slate-700">{{ $product->total_stock }}</span></p>

                        @if ($product->approval && $product->total_stock < $product->approval->minimum_quantity)
                            <p
                                class="mt-1 text-xs inline-flex items-center font-bold leading-sm uppercase px-2 py-0.5 bg-red-100 text-red-700 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 13a1 1 0 100-2 1 1 0 000 2zm-1.75-5.75a.75.75 0 00-1.5 0v3a.75.75 0 001.5 0v-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                Stok Rendah!
                            </p>
                        @endif

                        @if ($product->approval)
                            <div class="mt-4 space-y-3 border-t border-slate-200 pt-4">
                                <form action="{{ route('admin.products.updatePrice', $product->approval->id) }}" method="POST"
                                    class="space-y-1.5">
                                    @csrf
                                    <label for="custom_price_{{ $product->id }}"
                                        class="block text-xs font-medium text-slate-600">Harga Jual (Rp)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" id="custom_price_{{ $product->id }}" name="custom_price"
                                            value="{{ $product->approval->custom_price }}"
                                            class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm px-3 py-1.5">
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 shrink-0">Update</button>
                                    </div>
                                </form>

                                <form action="{{ route('admin.products.updateMinimumQuantity', $product->approval->id) }}"
                                    method="POST" class="space-y-1.5">
                                    @csrf
                                    <label for="min_quantity_{{ $product->id }}"
                                        class="block text-xs font-medium text-slate-600">Min. Kuantitas Beli</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" id="min_quantity_{{ $product->id }}" name="minimum_quantity"
                                            value="{{ $product->approval->minimum_quantity }}"
                                            class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm px-3 py-1.5">
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shrink-0">Update</button>
                                    </div>
                                </form>

                                <form action="{{ route('admin.products.updateShortDescription', $product->approval->id) }}"
                                    method="POST" class="space-y-1.5">
                                    @csrf
                                    <label for="short_desc_{{ $product->id }}"
                                        class="block text-xs font-medium text-slate-600">Deskripsi Singkat</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="short_desc_{{ $product->id }}" name="short_description"
                                            value="{{ $product->approval->short_description }}"
                                            class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm px-3 py-1.5">
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shrink-0">Update</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-4 border-t border-slate-200 rounded-b-xl">
                        <div class="flex flex-wrap gap-2 justify-start items-center">
                            @if (!$product->approval)
                                <form action="{{ route('admin.products.createApproval', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Ajukan Approval
                                    </button>
                                </form>
                            @else
                                @if ($product->approval->is_approved)
                                    <form action="{{ route('admin.products.unapprove', $product->approval->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Unapprove
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.products.approve', $product->approval->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if ($product->approval && $product->approval->is_approved)
                                <form action="{{ route('admin.products.toggleCustomForm', $product->approval->id) }}" method="POST"
                                    class="flex items-center gap-2">
                                    @csrf
                                    <label for="custom_form_toggle_{{$product->id}}"
                                        class="text-xs font-medium text-slate-600 sr-only">Form Kustom</label>
                                    <select name="is_custom_form" id="custom_form_toggle_{{$product->id}}"
                                        onchange="this.form.submit()"
                                        class="block w-auto border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 text-xs px-2 py-1.5 bg-white text-slate-700">
                                        <option value="0" {{ !$product->approval->is_custom_form ? 'selected' : '' }}>Form Kustom:
                                            Off</option>
                                        <option value="1" {{ $product->approval->is_custom_form ? 'selected' : '' }}>Form Kustom: On
                                        </option>
                                    </select>
                                </form>

                                <a href="{{ route('admin.custom-forms.index', $product->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Kelola Form
                                </a>
                                <a href="{{ route('admin.discount-tiers.index', $product->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                    Kelola Diskon
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 xl:col-span-3 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada produk</h3>
                    <p class="mt-1 text-sm text-slate-500">Silakan tambahkan produk baru untuk dikelola.</p>
                </div>
            @endforelse
        </div>

        @if ($products instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // This script remains unchanged as per your request
        @if ($products->count() > 0) // Check if products exist before iterating
            @foreach ($products as $product)
                @if ($product->approval && $product->total_stock < $product->approval->minimum_quantity)
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Rendah!',
                        text: 'Stok {{ addslashes($product->product_name) }} hanya {{ $product->total_stock }}, minimum beli {{ $product->approval->minimum_quantity }}.',
                        toast: true,
                        position: 'top-end',
                        timer: 5000,
                        showConfirmButton: false,
                        timerProgressBar: true
                    });
                @endif
            @endforeach
        @endif
    </script>
@endsection