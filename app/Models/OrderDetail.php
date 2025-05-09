<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price', 'discount', 'custom_form_data'
    ];

    protected $casts = [
        'custom_form_data' => 'array',
    ];
    public function product()
{
    return $this->belongsTo(Product::class);
}

}


