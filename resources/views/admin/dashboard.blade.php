@extends('layouts.admin')

@section('content')
    <div class="space-y-10 p-4 md:p-6 lg:p-8"> <h1 class="text-3xl font-bold text-slate-800">Dashboard Overview</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="{{ route('admin.products.index') }}"
               class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl flex items-start space-x-4 border border-transparent hover:border-blue-500">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-box-open fa-2x text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Products</h3>
                        <p class="text-4xl font-bold text-blue-600">{{ $totalProducts }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl flex items-start space-x-4 border border-transparent hover:border-green-500">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-shopping-cart fa-2x text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Orders</h3>
                        <p class="text-4xl font-bold text-green-600">{{ $totalOrders }}</p>
                    </div>
                </div>
            </a>

            <div class="bg-white p-6 rounded-xl shadow-lg flex items-start space-x-4 border border-transparent">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-tags fa-2x text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Discounts</h3>
                    <p class="text-4xl font-bold text-yellow-600">{{ $totalDiscounts }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg mt-10">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 sm:mb-0">Monthly Sales Performance</h2>
                @php
                    // ambil daftar tahun unik langsung di view
                    $availableYears = \Illuminate\Support\Facades\DB::table('orders')
                        ->selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year');
                @endphp
                @if($availableYears->isNotEmpty())
                    <div class="relative">
                        <select id="year-select"
                                class="appearance-none w-full sm:w-auto bg-slate-50 border border-slate-300 text-slate-700 py-2 px-4 pr-8 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                            @foreach ($availableYears as $y)
                                <option value="{{ $y }}" {{ (request()->query('year', now()->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                @endif
            </div>
            <div class="h-96"> <canvas id="salesChart"></canvas> </div>
        </div>

    </div> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <script>
        document.addEventListener('DOMContentLoaded', () => {
            const yearSelect = document.getElementById('year-select');
            const canvasElement = document.getElementById('salesChart');

            if (!canvasElement) {
                console.error('Sales Chart canvas element not found!');
                return;
            }
            const ctx = canvasElement.getContext('2d');
            let chartObj = null;

            function loadChart(selectedYear) {
                // If selectedYear is not provided, try to get from dropdown or default to current year
                if (selectedYear === undefined) {
                    if (yearSelect && yearSelect.value) {
                        selectedYear = yearSelect.value;
                    } else {
                        selectedYear = new Date().getFullYear();
                    }
                }

                // Your existing fetch logic
                fetch(`/api/monthly-sales?year=${selectedYear}`)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(({
                        labels,
                        data
                    }) => {
                        if (chartObj) {
                            chartObj.destroy();
                        }
                        chartObj = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: `Penjualan ${selectedYear} (Rp)`,
                                    data: data,
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)', // Tailwind blue-500 with opacity
                                    borderColor: 'rgba(59, 130, 246, 1)', // Tailwind blue-500
                                    borderWidth: 1,
                                    borderRadius: 4, // Rounded bars
                                    hoverBackgroundColor: 'rgba(37, 99, 235, 0.8)', // Tailwind blue-600 with opacity
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false, // Important for container height
                                animation: { // Added subtle animation
                                    duration: 800,
                                    easing: 'easeInOutQuart'
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            },
                                            color: '#475569' // text-slate-600
                                        },
                                        grid: {
                                            color: '#e2e8f0' // border-slate-200
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            color: '#475569' // text-slate-600
                                        },
                                        grid: {
                                            display: false // Hide x-axis grid lines
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#334155' // text-slate-700
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1e293b', // bg-slate-800
                                        titleColor: '#f1f5f9', // text-slate-100
                                        bodyColor: '#cbd5e1', // text-slate-300
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(err => console.error('Fetch error or chart rendering error:', err));
            }

            // Initial load
            loadChart(yearSelect ? yearSelect.value : new Date().getFullYear());

            // Event listener for year select
            if (yearSelect) {
                yearSelect.addEventListener('change', (e) => loadChart(e.target.value));
            }
        });
    </script>
@endsection
