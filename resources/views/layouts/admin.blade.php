<!DOCTYPE html>
<html lang="en" x-data="{ open: true }" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daycomp Admin Panel</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

  <!-- Alpine.js + Plugin Collapse -->
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.plugin(window.collapse)
    })
  </script>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
    }

    .tooltip {
      position: absolute;
      left: 100%;
      top: 50%;
      transform: translateY(-50%);
      margin-left: 0.5rem;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      background-color: #1f2937;
      color: white;
      border-radius: 0.25rem;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
      white-space: nowrap;
    }

    .group:hover .tooltip {
      opacity: 1;
    }
  </style>
</head>

<body class="flex min-h-screen bg-gray-100">

  <!-- Sidebar -->
  <aside :class="open ? 'w-64' : 'w-20'" x-data="{ open: true }"
    class="flex flex-col bg-white shadow-md transition-all duration-300 ease-in-out overflow-hidden h-screen fixed">

    <!-- Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
      <span x-show="open" class="text-xl font-bold text-blue-600 transition-opacity duration-200">Daycomp Admin</span>
      <button @click="open = !open" class="p-2 rounded hover:bg-gray-100 focus:outline-none">
        <svg :class="open ? '' : 'rotate-180'" class="h-6 w-6 text-gray-700 transition-transform duration-300"
          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-2 text-sm">
      @php
    $navClass = 'group flex items-center p-2 rounded-md transition text-gray-700 hover:bg-blue-100';
    $active = 'bg-blue-100 text-blue-700 font-semibold';
    @endphp

      <!-- Dashboard -->
      <a href="{{ route('admin.dashboard') }}"
        class="{{ request()->routeIs('admin.dashboard') ? $navClass . ' ' . $active : $navClass }}">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <span x-show="open" class="ml-3">Dashboard</span>
        <span x-show="!open" class="tooltip">Dashboard</span>
      </a>

      <!-- Products -->
      <a href="{{ route('admin.products.index') }}"
        class="{{ request()->routeIs('admin.products.index') ? $navClass . ' ' . $active : $navClass }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M20 13V6a2 2 0 00-2-2h-3M8 4H5a2 2 0 00-2 2v7m17 0v7a2 2 0 01-2 2h-3m-8 0H5a2 2 0 01-2-2v-7"
            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <span x-show="open" class="ml-3">Products</span>
        <span x-show="!open" class="tooltip">Products</span>
      </a>

      <!-- Orders -->
      <a href="{{ route('admin.orders.index') }}"
        class="{{ request()->routeIs('admin.orders.index') ? $navClass . ' ' . $active : $navClass }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 7h18M3 12h18m-6 5h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <span x-show="open" class="ml-3">Orders</span>
        <span x-show="!open" class="tooltip">Orders</span>
      </a>

      <!-- Profile -->
      <a href="{{ route('admin.profile.edit') }}"
        class="{{ request()->routeIs('admin.profile.edit') ? $navClass . ' ' . $active : $navClass }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M5.121 17.804A8 8 0 0112 4a8 8 0 016.879 13.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <span x-show="open" class="ml-3">Profile</span>
        <span x-show="!open" class="tooltip">Profile</span>
      </a>

      <!-- Logout -->
      <a href="/logout" class="group flex items-center p-2 rounded-md text-red-500 hover:bg-red-100 hover:text-red-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <span x-show="open" class="ml-3">Logout</span>
        <span x-show="!open" class="tooltip">Logout</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 ml-20 md:ml-64 p-6 transition-all duration-300">
    @yield('content')
  </div>

</body>

</html>