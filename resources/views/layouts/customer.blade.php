<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daycomp Ecommerce </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .nav-link-active {
            color: #2563eb;
            /* text-blue-600 */
            position: relative;
        }

        .nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            /* Adjust to position underline correctly below text */
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #2563eb;
            /* blue-600 */
        }

        .cart-badge {
            min-width: 20px;
            /* Ensure badge has a minimum width */
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body class="bg-slate-50 flex flex-col min-h-screen">

    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="{{ route('customer.home') }}" class="text-3xl font-extrabold tracking-tight">
                        <span class="text-slate-800">DayComp</span><span class="text-sky-600">Percetakan</span>
                    </a>
                </div>

                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="{{ route('customer.home') }}"
                        class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150 {{ request()->routeIs('customer.home') ? 'nav-link-active font-semibold' : '' }}">Home</a>
                    <a href="{{ route('customer.category', 1) }}"
                        class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150 {{ request()->is('category/1') ? 'nav-link-active font-semibold' : '' }}">Undangan</a>
                    <a href="{{ route('customer.category', 2) }}"
                        class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150 {{ request()->is('category/2') ? 'nav-link-active font-semibold' : '' }}">Stiker</a>
                    <a href="{{ route('customer.category', 3) }}"
                        class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150 {{ request()->is('category/3') ? 'nav-link-active font-semibold' : '' }}">Cetak
                        Foto</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('cart.view') }}"
                        class="group flex items-center text-slate-600 hover:text-sky-600 relative p-2 rounded-md hover:bg-slate-100 transition-colors duration-150">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.4-5H19M7 13l1.5 6h9l1.5-6M9 21h.01M15 21h.01"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class="ml-2 text-base font-medium">Keranjang</span>
                        <span id="cart-icon"
                            class="ml-2 bg-sky-500 text-white rounded-full text-xs font-bold cart-badge">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                    <!--
                    @guest
                        <a href="#" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150">Login</a> // Changed route('login') to #
                        @if (Route::has('register')) // This check itself implies 'register' route might exist. Kept as is.
                            <a href="#" class="ml-4 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-sky-600 hover:bg-sky-700"> // Changed route('register') to #
                                Register
                            </a>
                        @endif
                    @else
                        <a href="#" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150">Profile</a> // Changed route('user.profile') to #
                        <form method="POST" action="#"> // Changed route('logout') to #
                            @csrf
                            <a href="#" // Changed route('logout') to #
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-base font-medium transition-colors duration-150">
                                Logout
                            </a>
                        </form>
                    @endguest
                    -->
                </div>

                <div class="md:hidden flex items-center">
                    <a href="{{ route('cart.view') }}"
                        class="group flex items-center text-slate-600 hover:text-sky-600 relative p-2 rounded-md hover:bg-slate-100 transition-colors duration-150 mr-2">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.4-5H19M7 13l1.5 6h9l1.5-6M9 21h.01M15 21h.01"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span id="cart-icon-mobile"
                            class="absolute -top-1 -right-1 bg-sky-500 text-white rounded-full text-xs font-bold cart-badge">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                    <button type="button"
                        class="p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('customer.home') }}"
                    class="text-slate-700 hover:bg-slate-100 hover:text-sky-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('customer.home') ? 'bg-sky-100 text-sky-700 font-semibold' : '' }}">Home</a>
                <a href="{{ route('customer.category', 1) }}"
                    class="text-slate-700 hover:bg-slate-100 hover:text-sky-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('category/1') ? 'bg-sky-100 text-sky-700 font-semibold' : '' }}">Undangan</a>
                <a href="{{ route('customer.category', 2) }}"
                    class="text-slate-700 hover:bg-slate-100 hover:text-sky-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('category/2') ? 'bg-sky-100 text-sky-700 font-semibold' : '' }}">Stiker</a>
                <a href="{{ route('customer.category', 3) }}"
                    class="text-slate-700 hover:bg-slate-100 hover:text-sky-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('category/3') ? 'bg-sky-100 text-sky-700 font-semibold' : '' }}">Cetak
                    Foto</a>
            </div>
            <!--
            <div class="pt-4 pb-3 border-t border-slate-200">
                @guest
                    <div class="flex items-center px-5">
                        <a href="#" class="text-base font-medium text-slate-700 hover:text-sky-600">Login</a> // Changed route('login') to #
                    </div>
                    @if (Route::has('register')) // This check itself implies 'register' route might exist. Kept as is.
                        <div class="mt-3 px-2 space-y-1">
                            <a href="#" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-sky-600 hover:bg-sky-700">Register</a> // Changed route('register') to #
                        </div>
                    @endif
                @else
                    <div class="flex items-center px-5">
                         <div class="ml-3">
                            <div class="text-base font-medium text-slate-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-sky-600 hover:bg-slate-100">Your Profile</a> // Changed route('user.profile') to #
                        <form method="POST" action="#"> // Changed route('logout') to #
                            @csrf
                            <a href="#" // Changed route('logout') to #
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-sky-600 hover:bg-slate-100">
                                Sign out
                            </a>
                        </form>
                    </div>
                @endguest
            </div>
            -->
        </div>
    </nav>

    <main class="flex-grow">
        <div class="py-8 md:py-12">
            @yield('content')
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true, // only animate once
            duration: 800, // animation duration
            offset: 50, // offset (in px) from the original trigger point
        });

        // Basic Mobile Menu Toggle
        const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;
                mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('hidden'); // Toggle visibility
                // Toggle icons
                const icons = mobileMenuButton.querySelectorAll('svg');
                icons.forEach(icon => icon.classList.toggle('hidden'));
            });
        }
    </script>

    @yield('scripts')

</body>

</html>