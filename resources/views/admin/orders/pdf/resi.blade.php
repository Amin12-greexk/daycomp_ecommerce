<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Resi {{ $order->resi_code }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h2>Resi Pengiriman - {{ $order->resi_code }}</h2>
    <p><strong>Nama:</strong> {{ $order->name }}</p>
    <p><strong>Telepon:</strong> {{ $order->phone }}</p>
    <p><strong>Alamat:</strong> {{ $order->address }}</p>
    <hr>
    <h4>Produk:</h4>
    <ul>
        @foreach ($order->orderDetails as $item)
            <li>{{ $item->product->product_name ?? 'Produk' }} - Qty: {{ $item->quantity }}</li>
        @endforeach
    </ul>
    <p><strong>Total:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
</body>

</html>