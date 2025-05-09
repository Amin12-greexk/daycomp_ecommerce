@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Manage Custom Form - {{ $product->product_name }}</h1>

    <div class="mb-4">
        <a href="{{ route('admin.custom-forms.create', $product->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add New Field
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left">Field Label</th>
                    <th class="py-3 px-4 text-left">Type</th>
                    <th class="py-3 px-4 text-left">Required</th>
                    <th class="py-3 px-4 text-left">Order</th>
                    <th class="py-3 px-4 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customForms as $field)
                    <tr class="border-t">
                        <td class="py-3 px-4">{{ $field->field_label }}</td>
                        <td class="py-3 px-4">{{ ucfirst($field->field_type) }}</td>
                        <td class="py-3 px-4">{{ $field->is_required ? 'Yes' : 'No' }}</td>
                        <td class="py-3 px-4">{{ $field->field_order }}</td>
                        <td class="py-3 px-4 flex space-x-2">
                            <a href="{{ route('admin.custom-forms.edit', $field->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                Edit
                            </a>
                            <form action="{{ route('admin.custom-forms.destroy', $field->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
