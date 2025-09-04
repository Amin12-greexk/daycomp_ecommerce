@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Edit Tingkat Diskon</h1>
        <p class="text-sm text-slate-600 mt-1">Untuk Produk: <span
                class="font-semibold text-sky-700">{{ $discountTier->product->product_name }}</span></p>

        <div class="mt-8 bg-white shadow-xl rounded-lg p-6 sm:p-8">
            <form action="{{ route('admin.discount-tiers.update', $discountTier->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label for="min_quantity" class="block text-sm font-medium text-slate-700 mb-1">Kuantitas
                        Minimum</label>
                    <input type="number" name="min_quantity" id="min_quantity"
                        value="{{ old('min_quantity', $discountTier->min_quantity) }}" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                </div>
                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-slate-700 mb-1">Persentase Diskon
                        (%)</label>
                    <input type="number" step="0.01" name="discount_percentage" id="discount_percentage"
                        value="{{ old('discount_percentage', $discountTier->discount_percentage) }}" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.products.discount-tiers.index', $discountTier->product_id) }}"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-md text-white bg-sky-600 hover:bg-sky-700">
                        Update Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection