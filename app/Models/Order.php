<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'resi_code',
        'status',
        'total_price',
        'name',
        'phone',
        'address',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
