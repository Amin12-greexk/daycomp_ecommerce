@extends('layouts.customer')

@push('head')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous">
    <style>
        @keyframes scale-in {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scale-in 0.25s ease-out;
        }
    </style>
@endpush

@section('content')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .discount-badge {
            display: inline-block;
            background: #22c55e;
            color: white;
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            margin: 0.2rem 0;
        }

        .add-button {
            background: linear-gradient(to right, #3b82f6, #2563eb);
        }

        .add-button:hover {
            background: linear-gradient(to right, #2563eb, #1d4ed8);
        }

        .hero-bg {
            background: linear-gradient(to right, #3b82f6, #9333ea);
        }
    </style>

    <!-- Hero Section -->
    <section class="relative hero-bg text-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between py-20 px-6">
            <div class="space-y-6 text-center md:text-left" data-aos="fade-right">
                <h1 class="text-4xl md:text-6xl font-extrabold">Welcome to DayComp</h1>
                <p class="text-lg md:text-2xl font-medium">Temukan produk terbaik dengan harga terbaik!</p>
                <a href="#products"
                    class="inline-block bg-white text-blue-700 font-semibold px-6 py-3 rounded hover:bg-gray-200 transition-all shadow">
                    Lihat Produk
                </a>
            </div>
            <div class="mt-10 md:mt-0" data-aos="fade-left">
                <img src="{{ asset('hero.jpg') }}" alt="Hero"
                    class="w-full h-64 object-cover rounded-lg shadow-xl border-4 border-white">
            </div>
        </div>
    </section>

    <!-- Product Section -->
    <main id="products" class="max-w-7xl mx-auto p-6 md:p-10">
        <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-800 mb-10" data-aos="fade-up">
            Daftar Produk
        </h2>

        <!-- Filter Harga -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-10">
            <form method="GET" action="{{ route('customer.home') }}"
                class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <input type="number" name="min_price" placeholder="Harga Min" value="{{ request('min_price') }}"
                    class="w-full md:w-40 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    min="0">
                <input type="number" name="max_price" placeholder="Harga Max" value="{{ request('max_price') }}"
                    class="w-full md:w-40 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    min="0">
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                        Filter
                    </button>
                    @if(request()->has('min_price') || request()->has('max_price'))
                        <a href="{{ route('customer.home') }}"
                            class="text-sm text-red-600 underline hover:text-red-700 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($products as $product)
                    <div class="bg-white shadow-md rounded-2xl overflow-hidden flex flex-col transition hover:shadow-lg hover:scale-[1.02] duration-200"
                        data-aos="fade-up">
                        <div class="relative group">
                            <img src="{{ $product->image_url ?? asset('placeholder.jpg') }}" alt="{{ $product->product_name }}"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105 cursor-pointer"
                                onclick="showImageModal('{{ $product->image_url ?? asset('placeholder.jpg') }}')">
                        </div>

                        <div class="p-5 flex flex-col h-full">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 truncate">{{ $product->product_name }}</h3>
                            <p class="text-blue-600 font-semibold text-base mb-2">
                                Rp {{ number_format($product->approval->custom_price, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 mb-3">
                                Minimal beli: <span class="font-medium text-gray-700">{{ $product->approval->minimum_quantity }}
                                    pcs</span>
                            </p>

                            @php
                                $discountTiers = $product->discountTiers->sortBy('min_quantity');
                            @endphp
                            @if($discountTiers->count() > 0)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($discountTiers as $tier)
                                        <span class="text-xs font-medium bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                                            Beli {{ $tier->min_quantity }}+ → Diskon {{ $tier->discount_percentage }}%
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <form id="add-to-cart-form-{{ $product->id }}" action="{{ route('cart.add') }}" method="POST"
                                class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-md shadow hover:brightness-110 transition">
                                    <i class="fas fa-cart-plus mr-2"></i> Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
            @empty
                <div class="col-span-3 text-center text-gray-500">Tidak ada produk ditemukan.</div>
            @endforelse
        </div>
    </main>


    <!-- Modal Gambar -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
        <div class="relative max-w-4xl w-full px-4">
            <button onclick="closeImageModal()"
                class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
            <img id="modal-image" src="" alt="Preview Gambar"
                class="w-full max-h-[90vh] object-contain rounded animate-scale-in">
        </div>
    </div>
@endsection

@section('scripts')
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ once: true, duration: 800 });</script>

    <!-- SweetAlert + Cart AJAX -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[id^="add-to-cart-form-"]');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const cartIcon = document.getElementById('cart-icon');
                    const button = this.querySelector('button');

                    button.disabled = true;
                    button.innerText = 'Adding...';

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            cartIcon.innerText = data.cart_count;
                            cartIcon.classList.add('animate-bounce');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pesanan telah ditambahkan ke keranjang.',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            button.innerText = 'Ditambahkan!';
                            setTimeout(() => {
                                cartIcon.classList.remove('animate-bounce');
                                button.innerText = 'Tambah Ke Keranjang';
                                button.disabled = false;
                            }, 1000);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menambahkan ke keranjang.'
                            });
                            button.innerText = 'Add to Cart';
                            button.disabled = false;
                        });
                });
            });
        });

        function showImageModal(src) {
            const modal = document.getElementById('image-modal');
            const img = document.getElementById('modal-image');
            img.src = src;
            modal.classList.remove('hidden');
        }

        function closeImageModal() {
            const modal = document.getElementById('image-modal');
            const img = document.getElementById('modal-image');
            modal.classList.add('hidden');
            img.src = '';
        }

        document.getElementById('image-modal').addEventListener('click', function (e) {
            if (e.target.id === 'image-modal') closeImageModal();
        });
    </script>
@endsection