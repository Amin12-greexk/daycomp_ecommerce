<?php

// #####################################################################
// THIS IS THE CORRECTED CONTROLLER FOR YOUR E-COMMERCE PROJECT
// File: app/Http/Controllers/Admin/CustomFormController.php
// #####################################################################

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomForm;

class CustomFormController extends Controller
{
    /**
     * Display a listing of the custom form fields for a specific product.
     */
    public function index(Product $product)
    {
        $customForms = $product->customForms()->orderBy('field_order')->get();
        return view('admin.custom_forms.index', compact('product', 'customForms'));
    }

    /**
     * Show the form for creating a new custom form field.
     */
    public function create(Product $product)
    {
        return view('admin.custom_forms.create', compact('product'));
    }

    /**
     * Store a newly created custom form field in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,date,time,file',
            'is_required' => 'required|boolean',
            'field_order' => 'nullable|integer',
        ]);

        $product->customForms()->create($request->all());

        // THE FIX: Redirect to the correct nested route name.
        return redirect()->route('admin.products.custom-forms.index', $product->id)
            ->with('success', 'Field added successfully.');
    }

    /**
     * Show the form for editing the specified custom form field.
     */
    public function edit($id)
    {
        $field = CustomForm::with('product')->findOrFail($id);
        return view('admin.custom_forms.edit', compact('field'));
    }

    /**
     * Update the specified custom form field in storage.
     */
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

        // THE FIX: Redirect to the correct nested route name.
        return redirect()->route('admin.products.custom-forms.index', $field->product_id)
            ->with('success', 'Field updated successfully.');
    }

    /**
     * Remove the specified custom form field from storage.
     */
    public function destroy($id)
    {
        $field = CustomForm::findOrFail($id);
        $field->delete();

        return redirect()->back()->with('success', 'Field deleted successfully.');
    }
}
