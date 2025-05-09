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
        @php
            // ambil daftar tahun unik langsung di view
            $availableYears = \Illuminate\Support\Facades\DB::table('orders')
                ->selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
        @endphp
        <select id="year-select" class="border px-3 py-1 rounded">
            @foreach ($availableYears as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
        <canvas id="salesChart" height="120"></canvas>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const yearSelect = document.getElementById('year-select'); // opsional, dropdown tahun
                const canvas = document.getElementById('salesChart').getContext('2d');
                let chartObj = null;

                // fungsi ambil data dan render
                function loadChart(selectedYear = new Date().getFullYear()) {
                    fetch(`/api/monthly-sales?year=${selectedYear}`)
                        .then(res => res.json())
                        .then(({ labels, data }) => {

                            // hancurkan chart lama
                            if (chartObj) chartObj.destroy();

                            chartObj = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: `Penjualan ${selectedYear} (Rp)`,
                                        data,
                                        backgroundColor: 'rgba(59,130,246,0.7)',
                                        borderColor: 'rgba(59,130,246,1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    animation: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                            }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(err => console.error('Fetch error', err));
                }

                // panggil awal
                loadChart();

                // jika ada dropdown tahun, panggil ulang
                if (yearSelect) {
                    yearSelect.addEventListener('change', e => loadChart(e.target.value));
                }

            });
        </script>

@endsection