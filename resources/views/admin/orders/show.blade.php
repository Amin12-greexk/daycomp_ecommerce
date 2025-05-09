@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto py-10 px-6">
        <h1 class="text-2xl font-bold mb-8">Order Detail - {{ $order->resi_code }}</h1>

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Customer Info -->
            <div>
                <h3 class="text-lg font-semibold mb-2">Customer Info</h3>
                <p><strong>Name:</strong> {{ $order->name }}</p>
                <p><strong>Phone:</strong> {{ $order->phone }}</p>
                <p><strong>Address:</strong> {{ $order->address }}</p>
            </div>

            <!-- Order Items -->
            <div>
                <h3 class="text-lg font-semibold mb-2">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4">Product Name</th>
                                <th class="py-2 px-4">Quantity</th>
                                <th class="py-2 px-4">Price</th>
                                <th class="py-2 px-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $item)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $item->product->product_name ?? 'Deleted Product' }}</td>
                                    <td class="py-2 px-4">{{ $item->quantity }}</td>
                                    <td class="py-2 px-4">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-2 px-4">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Status -->
            <div>
                <h3 class="text-lg font-semibold mb-2">Order Status</h3>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                    class="flex items-center space-x-4">
                    @csrf
                    <select name="status" class="border px-3 py-2 rounded">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ $order->status == 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection