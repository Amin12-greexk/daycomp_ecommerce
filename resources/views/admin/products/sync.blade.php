@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Ambil Data dari Warehouse</h1>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sync Control Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-xl p-8">
                    <form action="{{ route('admin.sync.run') }}" method="POST" id="sync-form">
                        @csrf
                        <h2 class="text-xl font-semibold text-slate-800">Sinkronisasi Penuh</h2>
                        <p class="text-slate-600 mt-2 mb-6">
                            Klik tombol untuk menjalankan sinkronisasi
                        </p>

                        <div class="mb-6">
                            <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1">Sync Produk Baru
                                Sejak (Opsional)</label>
                            <input type="date" name="start_date" id="start_date"
                                class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-sky-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:border-sky-800 focus:ring focus:ring-sky-300 disabled:opacity-50 transition">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" id="sync-spinner">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Jalankan Sinkronisasi</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Last Sync Report & History Section -->
            <div class="lg:col-span-2 space-y-8">
                @if(session('last_sync_report'))
                    @php $report = session('last_sync_report'); @endphp
                    <div class="bg-white shadow-lg rounded-xl p-8 border-2 border-sky-500">
                        <h2 class="text-xl font-semibold text-slate-800 mb-4">Laporan Sinkronisasi Baru Saja</h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mb-6">
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <p class="text-sm font-medium text-blue-600">Stok Diperbarui</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $report['stock_updated_count'] ?? '0' }}</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg">
                                <p class="text-sm font-medium text-green-600">Baru Ditambahkan</p>
                                <p class="text-2xl font-bold text-green-800">{{ $report['added_count'] ?? '0' }}</p>
                            </div>
                            <div class="bg-amber-100 p-4 rounded-lg">
                                <p class="text-sm font-medium text-amber-600">Dilewati</p>
                                <p class="text-2xl font-bold text-amber-800">{{ $report['skipped_count'] ?? '0' }}</p>
                            </div>
                            <div class="bg-red-100 p-4 rounded-lg">
                                <p class="text-sm font-medium text-red-600">Dihapus</p>
                                <p class="text-2xl font-bold text-red-800">{{ $report['deleted_count'] ?? '0' }}</p>
                            </div>
                        </div>

                        @if(!empty($report['details']['new_products']))
                            <h3 class="text-lg font-semibold text-slate-700 mb-3">Detail Produk Baru:</h3>
                            <div class="border border-slate-200 rounded-lg overflow-hidden max-h-48 overflow-y-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama Produk
                                            </th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Kode</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        @foreach($report['details']['new_products'] as $product)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-slate-800">{{ $product['name'] }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-500">{{ $product['code'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if(!empty($report['details']['deleted_products']))
                            <h3 class="text-lg font-semibold text-slate-700 mt-6 mb-3">Detail Produk Dihapus:</h3>
                            <div class="border border-slate-200 rounded-lg overflow-hidden max-h-48 overflow-y-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama Produk
                                            </th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Kode</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        @foreach($report['details']['deleted_products'] as $product)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-slate-800">{{ $product['name'] }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-500">{{ $product['code'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Sync History Section -->
                <div class="bg-white shadow-lg rounded-xl p-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Riwayat Sinkronisasi</h2>
                    <div class="border border-slate-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Tanggal
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Ditambahkan
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Stok
                                        Diperbarui</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Dihapus
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-800">
                                            {{ $report->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-green-600">{{ $report->added_count }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-blue-600">{{ $report->stock_updated_count }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-red-600">{{ $report->deleted_count }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="{{ route('admin.sync.download', $report->id) }}"
                                                class="text-sky-600 hover:text-sky-800 font-medium">
                                                Download PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-slate-500 py-6">Belum ada riwayat sinkronisasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sync-form').addEventListener('submit', function () {
            document.getElementById('sync-spinner').classList.remove('hidden');
            const button = this.querySelector('button[type="submit"]');
            button.querySelector('span').innerText = 'Sinkronisasi...';
            button.disabled = true;
        });
    </script>
@endsection