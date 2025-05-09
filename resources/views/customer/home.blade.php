@extends('layouts.customer')

@section('content')
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .discount-badge {
            display: inline-block;
            background: #16a34a;
            color: white;
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            margin-top: 0.25rem;
        }

        .add-button {
            background: linear-gradient(to right, #3b82f6, #2563eb);
        }

        .add-button:hover {
            background: linear-gradient(to right, #2563eb, #1e40af);
        }
    </style>

    <!-- Hero Section -->
    <section class="relative bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between py-20 px-6">
            <div class="space-y-6 text-center md:text-left" data-aos="fade-right">
                <h1 class="text-4xl md:text-6xl font-bold">Welcome to Daycomp</h1>
                <p class="text-lg md:text-2xl">Temukan produk terbaik dengan harga terbaik!</p>
                <a href="#products"
                    class="inline-block bg-white text-blue-600 font-semibold px-6 py-3 rounded hover:bg-gray-200 transition-all">
                    Lihat Produk
                </a>
            </div>
            <div class="mt-10 md:mt-0" data-aos="fade-left">
                <img src="{{ asset('hero.jpg') }}" alt="Hero" class="w-full h-64 object-cover rounded-lg shadow-xl">
            </div>
        </div>
    </section>

    <!-- Product Section -->
    <main id="products" class="max-w-7xl mx-auto p-8">
        <h2 class="text-3xl font-bold mb-8 text-center" data-aos="fade-up">Daftar Produk</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($products as $product)
                    <div class="product-card bg-white shadow rounded-lg p-6 flex flex-col" data-aos="zoom-in-up">
                        <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                            class="w-full h-48 object-cover mb-4 rounded">

                        <h2 class="text-xl font-semibold mb-2 text-gray-800">{{ $product->product_name }}</h2>

                        <p class="text-gray-700 mb-1">
                            Harga: <strong>Rp {{ number_format($product->approval->custom_price, 0, ',', '.') }}</strong>
                        </p>

                        <p class="text-gray-700 mb-2">
                            Minimal Beli: <strong>{{ $product->approval->minimum_quantity }}</strong> pcs
                        </p>

                        @php
                            $discountTiers = $product->discountTiers->sortBy('min_quantity');
                        @endphp

                        @if($discountTiers->count() > 0)
                            <div class="text-sm mb-2">
                                @foreach($discountTiers as $tier)
                                    <span class="discount-badge">
                                        Beli {{ $tier->min_quantity }}+ → {{ $tier->discount_percentage }}% off
                                    </span><br>
                                @endforeach
                            </div>
                        @endif

                        <form id="add-to-cart-form-{{ $product->id }}" action="{{ route('cart.add') }}" method="POST"
                            class="flex flex-col mt-auto">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit"
                                class="add-button text-white px-4 py-2 rounded transition-all text-sm font-semibold">
                                Tambah Ke Keranjang
                            </button>
                        </form>
                    </div>
            @endforeach
        </div>
    </main>
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
                            if (!response.ok) {
                                throw new Error('Network error');
                            }
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
    </script>
@endsection