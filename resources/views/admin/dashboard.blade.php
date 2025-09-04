@extends('layouts.admin')

@section('content')

    {{-- Menambahkan Font Awesome untuk ikon yang digunakan di kartu --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="space-y-10 p-4 md:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Ringkasan Dasbor</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard.download_report') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-800 focus:outline-none focus:border-slate-800 focus:ring focus:ring-slate-300 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5 2a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H5zm0 2h10v7H5V4zM8 14a1 1 0 000-2H7a1 1 0 000 2h1zm-1-4a1 1 0 11-2 0 1 1 0 012 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Cetak Laporan
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('admin.products.index') }}"
                class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl flex items-start space-x-4 border border-transparent hover:border-blue-500">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-box-open fa-2x text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Produk</h3>
                        <p class="text-4xl font-bold text-blue-600">{{ $totalProducts }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.orders.index') }}"
                class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl flex items-start space-x-4 border border-transparent hover:border-green-500">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-shopping-cart fa-2x text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Pesanan</h3>
                        <p class="text-4xl font-bold text-green-600">{{ $totalOrders }}</p>
                    </div>
                </div>
            </a>

            <div class="bg-white p-6 rounded-xl shadow-lg flex items-start space-x-4 border border-transparent">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-tags fa-2x text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-1">Total Diskon</h3>
                    <p class="text-4xl font-bold text-yellow-600">{{ $totalDiscounts }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg mt-10">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 sm:mb-0">Kinerja Penjualan Bulanan</h2>
                @php
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
                                <option value="{{ $y }}" {{ (request()->query('year', now()->year) == $y) ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                @endif
            </div>
            <div class="h-96"> <canvas id="salesChart"></canvas> </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- LOGIKA BAGAN (CHART) ---
            const yearSelect = document.getElementById('year-select');
            const canvasElement = document.getElementById('salesChart');
            if (!canvasElement) {
                return console.error('Elemen canvas Sales Chart tidak ditemukan!');
            }
            const ctx = canvasElement.getContext('2d');
            let chartObj = null;

            function loadChart(selectedYear) {
                if (selectedYear === undefined) {
                    selectedYear = (yearSelect && yearSelect.value) ? yearSelect.value : new Date().getFullYear();
                }

                const baseUrl = "{{ route('api.monthly-sales') }}";
                const fetchUrl = `${baseUrl}?year=${selectedYear}`;

                fetch(fetchUrl)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
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
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    hoverBackgroundColor: 'rgba(37, 99, 235, 0.8)',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 800,
                                    easing: 'easeInOutQuart'
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: value => 'Rp ' + value.toLocaleString('id-ID'),
                                            color: '#475569'
                                        },
                                        grid: {
                                            color: '#e2e8f0'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            color: '#475569'
                                        },
                                        grid: {
                                            display: false
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#334155'
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1e293b',
                                        titleColor: '#f1f5f9',
                                        bodyColor: '#cbd5e1',
                                        callbacks: {
                                            label: context => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(err => console.error('Fetch error or chart rendering error:', err));
            }

            loadChart();

            if (yearSelect) {
                yearSelect.addEventListener('change', (e) => loadChart(e.target.value));
            }
        });
    </script>
@endsection