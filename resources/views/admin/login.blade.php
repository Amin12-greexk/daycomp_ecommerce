<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-...omitted-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    /* Custom slower pulse animation */
    @keyframes pulse-slow {

      0%,
      100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: .6;
        transform: scale(1.1);
      }
    }

    .animate-pulse-slow {
      animation: pulse-slow 2s ease-in-out infinite;
    }
  </style>
</head>

<body class="bg-gray-100">

  <div class="flex justify-center items-center min-h-screen px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
      <!-- Animated logo -->
      <div class="flex justify-center mb-4">
        <img src="{{ asset('dc.png') }}" alt="Logo"
          class="w-20 h-20 rounded-full object-cover border-4 border-blue-200 animate-pulse-slow shadow-md">
      </div>

      <!-- Animated Title -->
      <h1 class="text-3xl font-extrabold mb-6 text-center text-blue-600">
        Admin Login
      </h1>

      @if ($errors->any())
      <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
      </ul>
      </div>
    @endif

      <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <!-- Email field with icon -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Email</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
              <i class="fas fa-envelope"></i>
            </span>
            <input type="email" name="email" value="{{ old('email') }}"
              class="w-full border rounded px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-300" required
              autofocus />
          </div>
        </div>

        <!-- Password field with icon -->
        <div class="mb-6">
          <label class="block text-gray-700 mb-1">Password</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
              <i class="fas fa-lock"></i>
            </span>
            <input type="password" name="password"
              class="w-full border rounded px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-300"
              required />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <button type="submit"
            class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Login
          </button>
        </div>
      </form>
    </div>
  </div>

</body>

</html>