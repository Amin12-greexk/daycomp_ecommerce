@extends('layouts.admin')

@section('content')
<div class="max-w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Kelola Form Kustom</h1>
            <p class="text-sm text-slate-600 mt-1">Untuk Produk: <span class="font-semibold text-sky-700">{{ $product->product_name }}</span></p>
        </div>
        <a href="{{ route('admin.custom-forms.create', $product->id) }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Field Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
            <p class="font-bold">Oops!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm align-middle">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Label Field</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-left font-semibold">Tipe</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Wajib Diisi</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Urutan</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($customForms as $field)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-4 px-4 sm:px-6 text-slate-700 font-medium whitespace-nowrap">{{ $field->field_label }}</td>
                            <td class="py-4 px-4 sm:px-6 text-slate-600 whitespace-nowrap">{{ ucfirst($field->field_type) }}</td>
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($field->is_required)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-slate-600 text-center whitespace-nowrap">{{ $field->field_order }}</td>
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.custom-forms.edit', $field->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-150">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.custom-forms.destroy', $field->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus field ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 px-4 sm:px-6">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.03 1.123 0 1.131.094 1.976 1.057 1.976 2.192V7.5M8.25 7.5h7.5M8.25 7.5V9a.75.75 0 01-.75.75H5.625a.75.75 0 01-.75-.75V7.5m7.5 0V9A.75.75 0 0015 9.75h2.625a.75.75 0 00.75-.75V7.5m0 0V5.625A2.625 2.625 0 0015.75 3h-7.5A2.625 2.625 0 006 5.625V7.5m0 0h7.5" />
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125v-1.5c0-.983.796-1.782 1.766-1.782h15.718c.97 0 1.766.799 1.766 1.782v1.5a1.125 1.125 0 01-1.125 1.125m-17.25 0h17.25" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada field kustom</h3>
                                    <p class="mt-1 text-sm text-slate-500">Silakan tambahkan field baru untuk produk ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
