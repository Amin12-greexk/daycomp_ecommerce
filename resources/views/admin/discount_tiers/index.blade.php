@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6">
    <h1 class="text-2xl font-bold mb-8">Manage Discount Tiers - {{ $product->product_name }}</h1>

    <div class="mb-4">
        <a href="{{ route('admin.discount-tiers.create', $product->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add New Discount Tier
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4">Minimum Quantity</th>
                    <th class="py-3 px-4">Discount (%)</th>
                    <th class="py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discountTiers as $tier)
                    <tr class="border-t">
                        <td class="py-3 px-4">{{ $tier->min_quantity }}</td>
                        <td class="py-3 px-4">{{ $tier->discount_percentage }}%</td>
                        <td class="py-3 px-4 flex space-x-2">
                            <a href="{{ route('admin.discount-tiers.edit', $tier->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                Edit
                            </a>
                            <form action="{{ route('admin.discount-tiers.destroy', $tier->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
