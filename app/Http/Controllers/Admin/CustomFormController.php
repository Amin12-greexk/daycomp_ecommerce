<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomForm;

class CustomFormController extends Controller
{
    public function index(Product $product)
    {
        $customForms = $product->customForms()->orderBy('field_order')->get();
        return view('admin.custom_forms.index', compact('product', 'customForms'));
    }

    public function create(Product $product)
    {
        return view('admin.custom_forms.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,date,time,file',
            'is_required' => 'required|boolean',
            'field_order' => 'nullable|integer',
        ]);

        $product->customForms()->create($request->all());

        return redirect()->route('admin.custom-forms.index', $product->id)
                         ->with('success', 'Field added successfully.');
    }

    public function edit($id)
    {
        $field = CustomForm::findOrFail($id);
        return view('admin.custom_forms.edit', compact('field'));
    }

    public function update(Request $request, $id)
    {
        $field = CustomForm::findOrFail($id);

        $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,date,time,file',
            'is_required' => 'required|boolean',
            'field_order' => 'nullable|integer',
        ]);

        $field->update($request->all());

        return redirect()->route('admin.custom-forms.index', $field->product_id)
                         ->with('success', 'Field updated successfully.');
    }

    public function destroy($id)
    {
        $field = CustomForm::findOrFail($id);
        $field->delete();

        return redirect()->back()->with('success', 'Field deleted successfully.');
    }
}
