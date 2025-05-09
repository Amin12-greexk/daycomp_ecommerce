<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Resi Pemesanan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .company-info {
            text-align: left;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #16a34a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .footer {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <img src="{{ public_path('dc.jpg') }}" alt="Daycomp Logo" class="logo">
        </div>
        <div class="company-info">
            <strong>Daycomp</strong><br>
            Jl. Contoh Raya No.123, Kudus<br>
            Telepon: 0857-0000-0000<br>
            Email: info@daycomp.com
        </div>
    </div>

    <h1>Resi Pemesanan</h1>
    <p><strong>Resi:</strong> {{ $order->resi_code }}</p>
    <p><strong>Nama:</strong> {{ $order->name }}</p>
    <p><strong>No. Telepon:</strong> {{ $order->phone }}</p>
    <p><strong>Alamat:</strong> {{ $order->address }}</p>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $item)
                <tr>
                    <td>{{ $item->product->product_name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><strong>Pembayaran:</strong> {{ $order->payment_method == 'midtrans' ? 'Midtrans' : 'Bayar di Tempat' }}</p>
        <p><strong>Status:</strong> Menunggu Pembayaran</p>
    </div>
</body>

</html>