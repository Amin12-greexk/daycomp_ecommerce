@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Add New Field for {{ $product->product_name }}</h1>

    <form action="{{ route('admin.custom-forms.store', $product->id) }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Field Label</label>
            <input type="text" name="field_label" required class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Field Type</label>
            <select name="field_type" required class="border w-full px-3 py-2 rounded">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
                <option value="file">File Upload</option>
            </select>
        </div>

        <div>
            <label class="block font-medium">Is Required?</label>
            <select name="is_required" required class="border w-full px-3 py-2 rounded">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <div>
            <label class="block font-medium">Field Order (Optional)</label>
            <input type="number" name="field_order" class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
