@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Edit Field: {{ $field->field_label }}</h1>
                <p class="text-sm text-slate-600 mt-1">Untuk Produk: <span
                        class="font-semibold text-sky-700">{{ $field->product->product_name }}</span></p>
            </div>
            <!-- THE FIX: Use the correct nested route name -->
            <a href="{{ route('admin.products.custom-forms.index', $field->product_id) }}"
                class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 text-sm font-medium rounded-lg shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-slate-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Field
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-lg p-6 sm:p-8">
            <!-- THE FIX: Use the shallow route name for the form action -->
            <form action="{{ route('admin.custom-forms.update', $field->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="field_label" class="block text-sm font-medium text-slate-700 mb-1">Label Field <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="field_label" id="field_label"
                        value="{{ old('field_label', $field->field_label) }}" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                </div>

                <div>
                    <label for="field_type" class="block text-sm font-medium text-slate-700 mb-1">Tipe Field <span
                            class="text-red-500">*</span></label>
                    <select name="field_type" id="field_type" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                        <option value="text" {{ old('field_type', $field->field_type) == 'text' ? 'selected' : '' }}>Text
                        </option>
                        <option value="textarea" {{ old('field_type', $field->field_type) == 'textarea' ? 'selected' : '' }}>
                            Textarea</option>
                        <option value="date" {{ old('field_type', $field->field_type) == 'date' ? 'selected' : '' }}>Date
                        </option>
                        <option value="time" {{ old('field_type', $field->field_type) == 'time' ? 'selected' : '' }}>Time
                        </option>
                        <option value="file" {{ old('field_type', $field->field_type) == 'file' ? 'selected' : '' }}>File
                            Upload</option>
                    </select>
                </div>

                <div>
                    <label for="is_required" class="block text-sm font-medium text-slate-700 mb-1">Wajib Diisi? <span
                            class="text-red-500">*</span></label>
                    <select name="is_required" id="is_required" required
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                        <option value="1" {{ old('is_required', $field->is_required) == 1 ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ old('is_required', $field->is_required) == 0 ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <div>
                    <label for="field_order" class="block text-sm font-medium text-slate-700 mb-1">Urutan Field</label>
                    <input type="number" name="field_order" id="field_order"
                        value="{{ old('field_order', $field->field_order) }}"
                        class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm px-3 py-2.5">
                    <p class="mt-1 text-xs text-slate-500">Opsional. Digunakan untuk mengurutkan field pada form.</p>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection