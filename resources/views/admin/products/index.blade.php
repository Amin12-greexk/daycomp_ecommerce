@extends('layouts.admin')

@section('content')

<style>
    .product-cell {
        @apply border px-6 py-4 align-top;
    }

    .section-title {
        @apply text-sm font-semibold text-gray-600 mb-1;
    }

    .input-group {
        @apply flex items-center gap-2 mb-4;
    }

    .form-box {
        @apply bg-gray-50 p-4 rounded border mb-4;
    }

    .action-button {
        @apply px-4 py-2 text-sm font-medium rounded text-white;
    }

    .btn-blue {
        background-color: #2563eb;
    }

    .btn-blue:hover {
        background-color: #1d4ed8;
    }

    .btn-green {
        background-color: #16a34a;
    }

    .btn-green:hover {
        background-color: #15803d;
    }

    .btn-red {
        background-color: #dc2626;
    }

    .btn-red:hover {
        background-color: #b91c1c;
    }

    .btn-purple {
        background-color: #7c3aed;
    }

    .btn-purple:hover {
        background-color: #6d28d9;
    }

    .btn-indigo {
        background-color: #4f46e5;
    }

    .btn-indigo:hover {
        background-color: #4338ca;
    }

    .btn-teal {
        background-color: #0d9488;
    }

    .btn-teal:hover {
        background-color: #0f766e;
    }
</style>


@section('content')
    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Kelola Produk Ecommerce</h1>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-200 text-gray-800">
                    <tr>
                        <th class="py-4 px-6 border-r">Nama Produk</th>
                        <th class="py-4 px-6 border-r">Harga Beli (Warehouse)</th>
                        <th class="py-4 px-6 border-r">Harga Jual & Min Beli</th>
                        <th class="py-4 px-6 border-r">Status Approval</th>
                        <th class="py-4 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($products as $index => $product)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <!-- Product Name -->
                            <td class="py-4 px-6 font-medium text-gray-700 border-r">
                                {{ $product->product_name }}
                            </td>

                            <!-- Purchase Price (Warehouse) -->
                            <td class="py-4 px-6 border-r">
                                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                            </td>

                            <!-- Custom Price, Minimum Quantity, Short Description -->
                            <td class="py-4 px-6 border-r">
                                @if ($product->approval)
                                    <div class="space-y-4">
                                        <!-- Custom Price Form -->
                                        <div class="flex items-center justify-between border-t pt-4">
                                            <label class="text-sm font-semibold">Harga Jual</label>
                                            <form action="{{ route('admin.products.updatePrice', $product->approval->id) }}"
                                                method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="number" name="custom_price"
                                                    value="{{ $product->approval->custom_price }}"
                                                    class="border border-gray-300 px-4 py-2 rounded-md w-32" min="0">
                                                <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Update
                                                    Price</button>
                                            </form>
                                        </div>

                                        <!-- Short Description Form -->
                                        <div class="flex items-center justify-between border-t pt-4">
                                            <label class="text-sm font-semibold">Deskripsi Singkat</label>
                                            <form
                                                action="{{ route('admin.products.updateShortDescription', $product->approval->id) }}"
                                                method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="text" name="short_description"
                                                    value="{{ $product->approval->short_description }}"
                                                    class="border border-gray-300 px-4 py-2 rounded-md w-full"
                                                    placeholder="Deskripsi singkat..." maxlength="255">
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">Update
                                                    Desc</button>
                                            </form>
                                        </div>

                                        <!-- Minimum Quantity Form -->
                                        <div class="flex items-center justify-between border-t pt-4">
                                            <label class="text-sm font-semibold">Min. Beli</label>
                                            <form
                                                action="{{ route('admin.products.updateMinimumQuantity', $product->approval->id) }}"
                                                method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="number" name="minimum_quantity"
                                                    value="{{ $product->approval->minimum_quantity }}"
                                                    class="border border-gray-300 px-4 py-2 rounded-md w-32" min="1">
                                                <button type="submit"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">Update
                                                    Min</button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Status Approval Actions -->
                            <td class="py-4 px-6 border-r">
                                <div class="flex flex-col gap-3">
                                    @if (!$product->approval)
                                        <!-- Add to Approval -->
                                        <form action="{{ route('admin.products.createApproval', $product->id) }}" method="POST">
                                            @csrf
                                            <button
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm">Add to
                                                Approval</button>
                                        </form>
                                    @else
                                        @if ($product->approval->is_approved)
                                            <!-- Unapprove -->
                                            <form action="{{ route('admin.products.unapprove', $product->approval->id) }}"
                                                method="POST">
                                                @csrf
                                                <button
                                                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md text-sm">Unapprove</button>
                                            </form>
                                        @else
                                            <!-- Approve -->
                                            <form action="{{ route('admin.products.approve', $product->approval->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm">Approve</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- Actions (Manage Discount, Custom Form) -->
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-3">
                                    @if ($product->approval && $product->approval->is_approved)
                                        <!-- Manage Custom Form -->
                                        @if ($product->approval->is_custom_form)
                                            <a href="{{ route('admin.custom-forms.index', $product->id) }}"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-5 py-2 rounded-md">Manage
                                                Custom Form</a>
                                        @else
                                            <!-- Set Custom Form -->
                                            <form action="{{ route('admin.products.toggleCustomForm', $product->approval->id) }}"
                                                method="POST">
                                                @csrf
                                                <button
                                                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm">Set
                                                    Custom Form</button>
                                            </form>
                                        @endif

                                        <!-- Manage Discount -->
                                        <a href="{{ route('admin.discount-tiers.index', $product->id) }}"
                                            class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-5 py-2 rounded-md">Manage
                                            Discount</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection