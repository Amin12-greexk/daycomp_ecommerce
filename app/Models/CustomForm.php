<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'field_label',
        'field_type',
        'is_required',
        'field_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
