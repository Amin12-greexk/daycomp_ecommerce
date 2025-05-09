@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Kelola Produk Ecommerce</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <div class="bg-white shadow rounded-lg p-6 space-y-4">
                    @if ($product->image_url)
                        <div class="flex justify-center">
                            <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                                class="w-full max-h-48 object-cover rounded border border-gray-200">
                        </div>
                    @endif
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $product->product_name }}</h2>
                            <p class="text-sm text-gray-500">Harga Beli: Rp
                                {{ number_format($product->purchase_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @if ($product->approval && $product->approval->is_approved)
                            <span class="text-green-600 font-semibold text-sm">Approved</span>
                        @else
                            <span class="text-gray-400 text-sm">Not Approved</span>
                        @endif
                    </div>

                    <!-- Forms -->
                    @if ($product->approval)
                        <div class="space-y-3">
                            <form action="{{ route('admin.products.updatePrice', $product->approval->id) }}" method="POST"
                                class="flex gap-2 items-center">
                                @csrf
                                <label class="w-32 text-sm">Harga Jual</label>
                                <input type="number" name="custom_price" value="{{ $product->approval->custom_price }}"
                                    class="border rounded px-3 py-2 w-28">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">Update</button>
                            </form>

                            <form action="{{ route('admin.products.updateMinimumQuantity', $product->approval->id) }}" method="POST"
                                class="flex gap-2 items-center">
                                @csrf
                                <label class="w-32 text-sm">Min. Beli</label>
                                <input type="number" name="minimum_quantity" value="{{ $product->approval->minimum_quantity }}"
                                    class="border rounded px-3 py-2 w-28">
                                <button
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded">Update</button>
                            </form>

                            <form action="{{ route('admin.products.updateShortDescription', $product->approval->id) }}"
                                method="POST" class="flex gap-2 items-center">
                                @csrf
                                <label class="w-32 text-sm">Deskripsi</label>
                                <input type="text" name="short_description" value="{{ $product->approval->short_description }}"
                                    class="border rounded px-3 py-2 w-full">
                                <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">Update</button>
                            </form>
                        </div>
                    @endif

                    <!-- Approval & Actions -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        @if (!$product->approval)
                            <form action="{{ route('admin.products.createApproval', $product->id) }}" method="POST">
                                @csrf
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Add to
                                    Approval</button>
                            </form>
                        @else
                            @if ($product->approval->is_approved)
                                <form action="{{ route('admin.products.unapprove', $product->approval->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">Unapprove</button>
                                </form>
                            @else
                                <form action="{{ route('admin.products.approve', $product->approval->id) }}" method="POST">
                                    @csrf
                                    <button
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Approve</button>
                                </form>
                            @endif
                        @endif

                        @if ($product->approval && $product->approval->is_approved)
                            <a href="{{ route('admin.custom-forms.index', $product->id) }}"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm">Form</a>
                            <a href="{{ route('admin.discount-tiers.index', $product->id) }}"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm">Discount</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection