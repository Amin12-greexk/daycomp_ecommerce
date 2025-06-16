<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Resi Pemesanan - {{ $order->resi_code }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            /* Ukuran font dasar sedikit lebih besar */
            color: #374151;
            /* text-gray-700 */
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            /* bg-gray-50 */
        }

        .container {
            width: 100%;
            max-width: 800px;
            /* Lebar maksimum untuk konten */
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            /* bg-white */
            border-radius: 8px;
            /* rounded-lg */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* shadow-lg */
        }

        .header {
            display: flex;
            align-items: flex-start;
            /* Align items to the top */
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            /* border-gray-200 */
        }

        .logo {
            width: 120px;
            /* Logo sedikit lebih besar */
            height: auto;
        }

        .company-info {
            text-align: right;
            /* Info perusahaan di kanan */
            font-size: 12px;
            line-height: 1.6;
        }

        .company-info strong {
            font-size: 16px;
            color: #1f2937;
            /* text-gray-800 */
            display: block;
            margin-bottom: 4px;
        }

        .order-title {
            font-size: 24px;
            /* Judul lebih besar */
            font-weight: 700;
            margin-bottom: 15px;
            color: #1e40af;
            /* text-blue-800 - Warna primer yang lebih gelap */
            text-align: center;
        }

        .customer-details p,
        .order-summary p {
            margin-bottom: 6px;
            line-height: 1.6;
        }

        .customer-details strong,
        .order-summary strong {
            color: #4b5563;
            /* text-gray-600 */
            min-width: 100px;
            /* Lebar minimum untuk label */
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            /* border-gray-200 */
            padding: 10px 12px;
            /* Padding lebih nyaman */
            text-align: left;
        }

        thead {
            background-color: #f3f4f6;
            /* bg-gray-100 */
        }

        th {
            font-weight: 600;
            color: #374151;
            /* text-gray-700 */
            font-size: 12px;
            text-transform: uppercase;
            /* Header tabel uppercase */
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
            /* bg-gray-50 untuk baris genap */
        }

        tbody td {
            color: #4b5563;
            /* text-gray-600 */
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            /* text-gray-900 */
            margin-top: 25px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
            /* border-gray-300 */
        }

        .footer-summary {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            /* border-gray-200 */
            text-align: right;
            /* Total di kanan */
        }

        .footer-summary p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .footer-summary strong {
            color: #1f2937;
            /* text-gray-800 */
        }

        .grand-total strong {
            font-size: 18px;
            color: #1e40af;
            /* text-blue-800 */
        }

        .thank-you-note {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            /* text-gray-500 */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <img src="{{ public_path('dc.jpg') }}" alt="Daycomp Logo" class="logo">
            </div>
            <div class="company-info">
                <strong>Daycomp</strong>
                Serabi Kidul, Getassrabi, Kec. Gebog,<br>
                Kabupaten Kudus, Jawa Tengah 59333<br>
                Telepon: +62 859-7455-9988<br>
                Email: daycomp@gmail.com
            </div>
        </div>

        <h1 class="order-title">Resi Pemesanan</h1>

        <div class="customer-details">
            <p><strong>No. Resi:</strong> {{ $order->resi_code }}</p>
            <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d F Y, H:i') }}</p>
            <p><strong>Nama Pelanggan:</strong> {{ $order->name }}</p>
            <p><strong>No. Telepon:</strong> {{ $order->phone }}</p>
            <p><strong>Alamat </strong> {{ $order->address }}</p>
        </div>

        <h2 class="section-title">Detail Produk</h2>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $item)
                    <tr>
                        <td>{{ $item->product->product_name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-summary">
            <p><strong>Subtotal Produk:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p class="grand-total"><strong>Total Pembayaran:</strong> Rp
                {{ number_format($order->total_price, 0, ',', '.') }}
            </p>
            <p><strong>Metode Pembayaran:</strong>
                {{ $order->payment_method == 'midtrans' ? 'Online (Midtrans)' : 'Bayar di Tempat' }}</p>
            <p><strong>Status Pesanan:</strong> <span
                    style="text-transform: capitalize; font-weight:600;">{{ str_replace('_', ' ', $order->status ?? 'Menunggu Pembayaran') }}</span>
            </p>
        </div>

        <div class="thank-you-note">
            <p>Terima kasih telah berbelanja di Daycomp! <br> Simpan resi ini sebagai bukti pemesanan Anda.</p>
        </div>
    </div>
</body>

</html>