@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Add Discount Tier for {{ $product->product_name }}</h1>

    <form action="{{ route('admin.discount-tiers.store', $product->id) }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Minimum Quantity</label>
            <input type="number" name="min_quantity" required min="1" class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Discount Percentage (%)</label>
            <input type="number" name="discount_percentage" required min="0" max="100" step="0.01" class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
