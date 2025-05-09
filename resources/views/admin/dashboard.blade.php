@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            <!-- Total Products -->
            <a href="{{ route('admin.products.index') }}" class="block hover:shadow-lg transition duration-200">
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center hover:bg-blue-50 cursor-pointer">
                    <h3 class="text-xl font-semibold mb-2 text-blue-600">Total Products</h3>
                    <p class="text-3xl font-bold text-gray-700">{{ $totalProducts }}</p>
                </div>
            </a>

            <!-- Total Orders -->
            <a href="{{ route('admin.orders.index') }}" class="block hover:shadow-lg transition duration-200">
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center hover:bg-green-50 cursor-pointer">
                    <h3 class="text-xl font-semibold mb-2 text-green-600">Total Orders</h3>
                    <p class="text-3xl font-bold text-gray-700">{{ $totalOrders }}</p>
                </div>
            </a>

            <!-- Total Discounts (non-clickable) -->
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                <h3 class="text-xl font-semibold mb-2 text-yellow-600">Total Discounts</h3>
                <p class="text-3xl font-bold text-gray-700">{{ $totalDiscounts }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-6 text-gray-800">Monthly Sales</h3>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [5, 10, 8, 15, 12, 20],
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection