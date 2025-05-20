<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\OrderApproved;
use Illuminate\Support\Facades\Mail;
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
            'status' => 'required|in:pending,paid,cancelled',
        ]);
        $order->status = $request->status;
        $order->save();
        if ($request->status === 'paid') {
            if (!empty($order->email)) {
                Mail::to($order->email)->send(new OrderApproved($order));
            }
        }

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
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

    public function Resi(Order $order)
    {
        $pdf = Pdf::loadView('admin.orders.pdf.resi', compact('order'));
        return $pdf->download("resi_{$order->resi_code}.pdf");
    }

    public function downloadCustomForm(Order $order)
    {
        $orderDetails = $order->orderDetails;

        foreach ($orderDetails as $detail) {
            if ($detail->custom_form_data) {
                foreach ($detail->custom_form_data as $fieldId => $value) {
                    $field = \App\Models\CustomForm::find($fieldId);
                    $detail->custom_form_data_labelled[$field->field_label ?? 'Field ' . $fieldId] = $value;
                }
            }
        }

        $pdf = Pdf::loadView('admin.orders.pdf.custom_form', compact('order'));
        return $pdf->download("formulir_{$order->resi_code}.pdf");
    }



}
