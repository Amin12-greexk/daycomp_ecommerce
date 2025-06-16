@extends('layouts.admin')

@section('content')

{{-- Add Font Awesome for the icons used in the cards --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="space-y-10 p-4 md:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <h1 class="text-3xl font-bold text-slate-800 mb-4 sm:mb-0">Dashboard Overview</h1>
        <!-- This button will appear if browser notification permission is needed -->
        <button id="request-permission" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 hidden transition-opacity duration-300">
            Enable Notifications
        </button>
    </div>

    <!-- This alert will appear on the page when a new product is detected -->
    <div id="new-product-alert" class="hidden p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg dark:bg-gray-800 dark:text-green-400" role="alert">
        <!-- Alert message will be inserted here by JavaScript -->
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Total Products Card -->
        <a href="{{ route('admin.products.index') }}" class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
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

        <!-- Total Orders Card -->
        <a href="{{ route('admin.orders.index') }}" class="block group transition-all duration-300 ease-in-out transform hover:-translate-y-1">
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

        <!-- Total Discounts Card -->
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

    <!-- Monthly Sales Chart -->
    <div class="bg-white p-6 rounded-xl shadow-lg mt-10">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-4 sm:mb-0">Monthly Sales Performance</h2>
            @php
                $availableYears = \Illuminate\Support\Facades\DB::table('orders')
                    ->selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year');
            @endphp
            @if($availableYears->isNotEmpty())
                <div class="relative">
                    <select id="year-select" class="appearance-none w-full sm:w-auto bg-slate-50 border border-slate-300 text-slate-700 py-2 px-4 pr-8 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
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

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- NOTIFICATION POLLING LOGIC ---
        let lastCheckTimestamp = "{{ $lastProductTimestamp ?? now()->toIso8601String() }}";
        const permissionButton = document.getElementById('request-permission');
        const newProductAlert = document.getElementById('new-product-alert');

        function showNotification(product) {
            // 1. Show a browser notification (if permission is granted)
            if (Notification.permission === "granted") {
                new Notification('Produk Baru di Gudang!', {
                    body: `Produk: ${product.product_name}`,
                    // You can add a link to your store icon here
                    icon: "https://placehold.co/48x48/3B82F6/FFFFFF?text=i"
                });
            }
            // 2. Show an alert on the page
            newProductAlert.innerHTML = `<span class="font-medium">Produk baru ditambahkan!</span> Nama: ${product.product_name}. <a href="{{ route('admin.products.index') }}" class="font-semibold underline hover:no-underline">Lihat daftar produk.</a>`;
            newProductAlert.classList.remove('hidden');
        }

        function checkForNewProducts() {
            const url = `{{ route('admin.products.check_new') }}?since=${encodeURIComponent(lastCheckTimestamp)}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.latestTimestamp) {
                        lastCheckTimestamp = data.latestTimestamp;
                    }
                    if (data.newProducts && data.newProducts.length > 0) {
                        data.newProducts.forEach(product => {
                            showNotification(product);
                        });
                    }
                })
                .catch(error => console.error('Error polling for new products:', error));
        }

        function startPolling() {
            setInterval(checkForNewProducts, 15000); // Check every 15 seconds
        }

        function handleNotificationPermission() {
            if (!("Notification" in window)) {
                console.log("This browser does not support desktop notification.");
            } else if (Notification.permission === "granted") {
                startPolling(); // Permission already granted
            } else if (Notification.permission !== "denied") {
                permissionButton.classList.remove('hidden'); // Show button to ask for permission
                permissionButton.onclick = function() {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            new Notification("Notifikasi Diaktifkan!", { body: "Anda akan diberitahu jika ada produk baru." });
                            permissionButton.classList.add('hidden');
                            startPolling();
                        }
                    });
                };
            }
        }

        handleNotificationPermission(); // Initialize on page load

        // --- CHART LOGIC ---
        const yearSelect = document.getElementById('year-select');
        const canvasElement = document.getElementById('salesChart');

        if (!canvasElement) {
            return console.error('Sales Chart canvas element not found!');
        }
        const ctx = canvasElement.getContext('2d');
        let chartObj = null;

        function loadChart(selectedYear) {
            if (selectedYear === undefined) {
                selectedYear = (yearSelect && yearSelect.value) ? yearSelect.value : new Date().getFullYear();
            }

            // **FIX**: Generate a full, reliable URL using Laravel's route() helper.
            const baseUrl = "{{ route('api.monthly-sales') }}";
            const fetchUrl = `${baseUrl}?year=${selectedYear}`;

            fetch(fetchUrl) // **FIX**: Use the new reliable URL.
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(({ labels, data }) => {
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
                            animation: { duration: 800, easing: 'easeInOutQuart' },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => 'Rp ' + value.toLocaleString('id-ID'),
                                        color: '#475569'
                                    },
                                    grid: { color: '#e2e8f0' }
                                },
                                x: {
                                    ticks: { color: '#475569' },
                                    grid: { display: false }
                                }
                            },
                            plugins: {
                                legend: { labels: { color: '#334155' } },
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
