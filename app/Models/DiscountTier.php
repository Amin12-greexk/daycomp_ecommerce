<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'discount_percentage',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
