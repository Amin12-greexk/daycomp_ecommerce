<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    xintegrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    /* Custom subtle pulse for the logo */
    @keyframes subtle-pulse {

      0%,
      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.3);
        /* blue-500 with opacity */
      }

      50% {
        transform: scale(1.05);
        box-shadow: 0 0 10px 10px rgba(59, 130, 246, 0);
      }
    }

    .animate-subtle-pulse {
      animation: subtle-pulse 2.5s ease-in-out infinite;
    }

    /* Custom gradient background */
    .gradient-bg {
      background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
      /* sky-100 to sky-50 */
    }

    /* Custom focus ring color */
    .focus\:ring-custom-blue-300:focus {
      --tw-ring-opacity: 1;
      --tw-ring-color: rgba(147, 197, 253, var(--tw-ring-opacity));
      /* Equivalent to blue-300 */
      box-shadow: 0 0 0 2px var(--tw-ring-color);
    }

    /* Styling for the error message box (from original user code, adapted for Tailwind) */
    .error-box-blade {
      background-color: #fee2e2;
      /* bg-red-100 */
      color: #b91c1c;
      /* text-red-700 */
      padding: 1rem;
      /* p-4 */
      border-radius: 0.375rem;
      /* rounded */
      margin-bottom: 1rem;
      /* mb-4 */
    }

    .error-box-blade ul {
      list-style-type: disc;
      padding-left: 1.25rem;
      /* pl-5 */
    }

    .error-box-blade li {
      /* space-y-1 was on ul, direct li margin might be better if needed */
    }
  </style>
</head>

<body class="gradient-bg">

  <div class="flex justify-center items-center min-h-screen px-4 py-8">
    <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl">
      <div class="flex justify-center mb-6">
        <img src="{{ asset('dc.png') }}" alt="Logo"
          class="w-24 h-24 rounded-full object-cover border-4 border-sky-200 animate-subtle-pulse shadow-lg">
      </div>

      <h1 class="text-3xl md:text-4xl font-bold mb-2 text-center text-slate-800">
        Admin Login
      </h1>
      <p class="text-center text-slate-500 mb-8">Silakan Login terlebih Dahulu.</p>

      @if ($errors->any())
      <div class="error-box-blade">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
      </ul>
      </div>
    @endif

      <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-5">
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
          <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <i class="fas fa-envelope"></i>
            </div>
            <input type="email" name="email" id="email"
              class="w-full border-slate-300 rounded-lg px-4 py-3 pl-10 focus:outline-none focus:ring-2 focus:ring-custom-blue-300 focus:border-sky-500 transition duration-150 ease-in-out"
              placeholder="akun@domain.com" required value="{{ old('email') }}" autofocus>
          </div>
        </div>

        <div class="mb-6">
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <i class="fas fa-lock"></i>
            </div>
            <input type="password" name="password" id="password"
              class="w-full border-slate-300 rounded-lg px-4 py-3 pl-10 focus:outline-none focus:ring-2 focus:ring-custom-blue-300 focus:border-sky-500 transition duration-150 ease-in-out"
              placeholder="••••••••" required>
          </div>
        </div>

        <div>
          <button type="submit"
            class="w-full flex items-center justify-center bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition duration-150 ease-in-out group">
            <i
              class="fas fa-sign-in-alt mr-2 transform transition-transform duration-150 group-hover:translate-x-1"></i>
            Login
          </button>
        </div>
      </form>

    </div>
  </div>
</body>

</html>