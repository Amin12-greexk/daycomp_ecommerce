@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-2xl font-bold mb-8">Order List</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-6 flex items-center space-x-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama customer..."
                class="border rounded-lg px-4 py-2 w-64 focus:outline-none focus:ring focus:border-blue-300" />
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                Cari
            </button>
        </form>


        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4">Resi Code</th>
                        <th class="py-3 px-4">Customer Name</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Total Price</th>
                        <th class="py-3 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-t">
                            <td class="py-3 px-4 font-semibold text-gray-700">{{ $order->resi_code }}</td>
                            <td class="py-3 px-4">{{ $order->name }}</td>
                            <td class="py-3 px-4 capitalize">{{ $order->status }}</td>
                            <td class="py-3 px-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection