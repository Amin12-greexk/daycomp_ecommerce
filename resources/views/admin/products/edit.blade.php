@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Edit Produk: {{ $product->product_name }}</h1>

        <div class="bg-white shadow-lg rounded-xl p-8">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Sale Price -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-slate-700">Harga Jual (Rp)</label>
                        <div class="mt-1">
                            <input type="number" name="sale_price" id="sale_price"
                                value="{{ old('sale_price', $product->sale_price) }}"
                                class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Minimum Quantity -->
                    <div>
                        <label for="minimum_quantity" class="block text-sm font-medium text-slate-700">Min. Kuantitas
                            Beli</label>
                        <div class="mt-1">
                            <input type="number" name="minimum_quantity" id="minimum_quantity"
                                value="{{ old('minimum_quantity', $product->minimum_quantity) }}"
                                class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-slate-700">Deskripsi
                            Singkat</label>
                        <div class="mt-1">
                            <input type="text" name="short_description" id="short_description"
                                value="{{ old('short_description', $product->short_description) }}"
                                class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Is Custom Form -->
                    <div>
                        <label for="is_custom_form" class="block text-sm font-medium text-slate-700">Gunakan Form
                            Kustom</label>
                        <select id="is_custom_form" name="is_custom_form"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="1" {{ old('is_custom_form', $product->is_custom_form) == 1 ? 'selected' : '' }}>Ya
                            </option>
                            <option value="0" {{ old('is_custom_form', $product->is_custom_form) == 0 ? 'selected' : '' }}>
                                Tidak</option>
                        </select>
                    </div>

                </div>

                <div class="mt-8 pt-5 border-t border-slate-200">
                    <div class="flex justify-end">
                        <a href="{{ route('admin.products.index') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Related Management Links -->
        <div class="mt-8 bg-white shadow-lg rounded-xl p-8">
            <h2 class="text-xl font-semibold text-slate-800 mb-4">Kelola Terkait</h2>
            <div class="flex flex-wrap gap-4">
                <!-- THE FIX: Use the full nested route name -->
                <a href="{{ route('admin.products.custom-forms.index', $product->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Kelola Form Kustom
                </a>
                <!-- THE FIX: Use the full nested route name -->
                <a href="{{ route('admin.products.discount-tiers.index', $product->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Kelola Diskon
                </a>
            </div>
        </div>
    </div>
@endsection