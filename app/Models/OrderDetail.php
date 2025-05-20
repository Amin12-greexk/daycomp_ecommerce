<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'custom_form_data'
    ];

    protected $casts = [
        'custom_form_data' => 'array',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCustomFormDataLabelledAttribute()
    {
        if (!is_array($this->custom_form_data)) {
            return [];
        }

        return collect($this->custom_form_data)->mapWithKeys(function ($value, $fieldId) {
            $field = \App\Models\CustomForm::find($fieldId);
            return [
                $field->field_label ?? 'Field ' . $fieldId => $value
            ];
        })->toArray();
    }


}


