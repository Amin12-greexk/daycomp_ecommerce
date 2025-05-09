<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request; 


class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Warehouse products table

    protected $fillable = [
        'product_name',
        'product_price',
        'product_image',
        'category_id',
        'warehouse_id',
        'stock',
    ];

    // ADD THIS RELATIONSHIP
    public function approval()
    {
        return $this->hasOne(ProductApproval::class, 'product_id');
    }

        public function customForms()
    {
        return $this->hasMany(CustomForm::class, 'product_id');
    }

    public function updateMinimumQuantity(Request $request, $approval_id)
    {
        $request->validate([
            'minimum_quantity' => 'required|integer|min:1',
        ]);

        $approval = \App\Models\ProductApproval::findOrFail($approval_id);
        $approval->minimum_quantity = $request->minimum_quantity;
        $approval->save();

        return redirect()->back()->with('success', 'Minimum purchase quantity updated successfully.');
    }

    public function discountTiers()
    {
        return $this->hasMany(DiscountTier::class, 'product_id');
    }


}
