<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ThankYouController extends Controller
{
    public function show($id)
{
    $order = Order::with('orderDetails')->findOrFail($id);

    return view('customer.thankyou', compact('order'));
}
}
