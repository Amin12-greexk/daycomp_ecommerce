<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('orderItems');

        if ($request->has('q') && $request->q != '') {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $orders = $query->orderByDesc('created_at')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }


    public function show(Order $order)
    {
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processed,completed',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $orders = Order::with(['items.product'])
            ->where('name', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.search', compact('orders', 'query'));
    }

    public function downloadResi(Order $order)
    {
        $order->load('orderDetails.product');

        $pdf = Pdf::loadView('pdf.order_receipt', compact('order'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('resi-' . $order->resi_code . '.pdf');
    }

}
