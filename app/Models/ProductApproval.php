<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'is_approved',
        'is_custom_form',
        'custom_price',
        'minimum_quantity',
        'short_description',

    ];
    

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
