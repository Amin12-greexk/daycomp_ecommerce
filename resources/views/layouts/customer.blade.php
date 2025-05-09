<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daycomp Ecommerce</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true, // hanya animasi pertama kali scroll
            duration: 800,
        });
    </script>


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('customer.home') }}" class="text-2xl font-bold">
                <span class="text-black">DayComp</span><span class="text-blue-600">Percetakan</span>
            </a>
        </div>

        <!-- Center: Menu -->
        <div class="flex items-center space-x-6 text-gray-700 font-semibold text-lg">
            <a href="{{ route('customer.home') }}"
                class="hover:text-blue-600 {{ request()->is('/') ? 'text-blue-600' : '' }}">Home</a>
            <a href="{{ route('customer.category', 1) }}" class="hover:text-blue-600">Undangan</a>
            <a href="{{ route('customer.category', 2) }}" class="hover:text-blue-600">Stiker</a>
            <a href="{{ route('customer.category', 3) }}" class="hover:text-blue-600">Cetak Foto</a>
        </div>


        <!-- Right: Cart -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('cart.view') }}" class="flex items-center text-gray-700 hover:text-blue-600 relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.4-5H19M7 13l1.5 6h9l1.5-6M9 21h.01M15 21h.01"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span class="ml-2">Keranjang</span>
                <span id="cart-icon" class="ml-2 bg-blue-500 text-white rounded-full px-2 py-0.5 text-xs">
                    {{ session('cart') ? count(session('cart')) : 0 }}
                </span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Page Specific Scripts -->
    @yield('scripts')

</body>

</html>