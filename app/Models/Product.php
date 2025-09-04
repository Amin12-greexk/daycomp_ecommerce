<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_code',
        'product_name',
        'image_url',
        'date_in',
        'is_approved',
        'is_custom_form',
        'sale_price',
        'short_description',
        'minimum_quantity',
        'category_id',
    ];

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // Add this method inside your app/Models/Product.php class
    public function discountTiers()
    {
        return $this->hasMany(\App\Models\DiscountTier::class);
    }

    public function customForms()
    {
        return $this->hasMany(\App\Models\CustomForm::class);
    }
}