<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daycomp Admin Panel - Upgraded</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!--
    <script>
        document.addEventListener('alpine:init', () => {
            // Alpine.plugin(window.collapse) // This line was removed
        })
    </script>
    -->

  <style>
    body {
      font-family: 'Inter', sans-serif;
      /* Updated font */
    }

    .tooltip {
      position: absolute;
      left: 100%;
      top: 50%;
      transform: translateY(-50%);
      margin-left: 12px;
      /* Increased margin slightly */
      padding: 6px 10px;
      /* Adjusted padding */
      font-size: 0.8rem;
      /* Slightly adjusted font size */
      background-color: #1f2937;
      /* bg-slate-800 */
      color: white;
      border-radius: 0.375rem;
      /* rounded-md */
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
      /* Smoother transition */
      white-space: nowrap;
      z-index: 50;
      /* Ensure tooltip is on top */
    }

    .group:hover .tooltip {
      opacity: 1;
      transform: translateY(-50%) translateX(0px);
      /* Optional: slight move out effect */
    }

    /* Custom scrollbar for Webkit browsers (optional enhancement) */
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      /* slate-300 */
      border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
      /* slate-400 */
    }
  </style>
</head>

<body x-data="{ sidebarOpen: true }" class="flex min-h-screen bg-slate-100 text-slate-800">

  <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="flex flex-col bg-white shadow-xl transition-all duration-300 ease-in-out h-screen fixed top-0 left-0 z-40 custom-scrollbar overflow-y-auto">

    <div class="flex items-center h-16 px-4 border-b border-slate-200 shrink-0">
      <span x-show="sidebarOpen"
        class="text-xl font-semibold text-sky-600 transition-opacity duration-300 ease-in-out ml-1">Daycomp Admin</span>

      <button @click="sidebarOpen = !sidebarOpen"
        class="ml-auto p-2 rounded-md text-slate-500 hover:bg-slate-100 hover:text-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">
        <span class="sr-only">Toggle sidebar</span>
        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300"
          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300"
          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
      </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1.5 text-sm">
      @php
    $baseNavClass = 'group relative flex items-center py-2.5 px-3 rounded-lg transition-colors duration-150 ease-in-out';
    $textClass = 'text-slate-600 hover:text-slate-900';
    $hoverBgClass = 'hover:bg-slate-100';
    // Updated active class for more visual emphasis
    $activeClass = 'bg-sky-500 shadow-sm text-white font-semibold';
    $inactiveClass = $textClass . ' ' . $hoverBgClass;
  @endphp

      <a href="{{ route('admin.dashboard') }}"
        class="{{ $baseNavClass }} {{ request()->routeIs('admin.dashboard') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span x-show="sidebarOpen" class="transition-opacity duration-200">Dashboard</span>
        <span x-show="!sidebarOpen" class="tooltip">Dashboard</span>
      </a>

      <a href="{{ route('admin.products.index') }}"
        class="{{ $baseNavClass }} {{ request()->routeIs('admin.products.index*') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <span x-show="sidebarOpen" class="transition-opacity duration-200">Products</span>
        <span x-show="!sidebarOpen" class="tooltip">Products</span>
      </a>

      <a href="{{ route('admin.orders.index') }}"
        class="{{ $baseNavClass }} {{ request()->routeIs('admin.orders.index*') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span x-show="sidebarOpen" class="transition-opacity duration-200">Orders</span>
        <span x-show="!sidebarOpen" class="tooltip">Orders</span>
      </a>

      <hr class="my-3 border-slate-200" x-show="sidebarOpen" />

      <a href="{{ route('admin.profile.edit') }}"
        class="{{ $baseNavClass }} {{ request()->routeIs('admin.profile.edit') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span x-show="sidebarOpen" class="transition-opacity duration-200">Profile</span>
        <span x-show="!sidebarOpen" class="tooltip">Profile</span>
      </a>

      <div class="!mt-auto pt-4"> <a href="admin/login"
          class="{{ $baseNavClass }} text-red-500 hover:bg-red-50 hover:text-red-700">
          <svg class="w-5 h-5 flex-shrink-0 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span x-show="sidebarOpen" class="transition-opacity duration-200">Logout</span>
          <span x-show="!sidebarOpen" class="tooltip">Logout</span>
        </a>
      </div>
    </nav>
  </aside>

  <main class="flex-1 transition-all duration-300 ease-in-out" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
    <div class="p-4 sm:p-6 lg:p-8"> @yield('content')
    </div>
  </main>

</body>

</html>