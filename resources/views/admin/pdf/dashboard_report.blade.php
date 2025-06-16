<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Dasbor</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #222;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .summary-section {
            margin-bottom: 25px;
            width: 100%;
            overflow: auto;
        }

        .summary-card {
            float: left;
            width: 30%;
            margin: 0 1.5%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #444;
            text-transform: uppercase;
        }

        .summary-card p {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #222;
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sales-table th,
        .sales-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .sales-table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        .sales-table h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #888;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Dasbor</h1>
            <p>Dibuat pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        </div>

        <div class="summary-section clearfix">
            <div class="summary-card">
                <h3>Total Produk</h3>
                <p>{{ $totalProducts }}</p>
            </div>
            <div class="summary-card">
                <h3>Total Pesanan</h3>
                <p>{{ $totalOrders }}</p>
            </div>
            <div class="summary-card">
                <h3>Total Diskon</h3>
                <p>{{ $totalDiscounts }}</p>
            </div>
        </div>

        <div class="sales-section">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th colspan="2">
                            <h2>Kinerja Penjualan Bulanan ({{ $salesData['year'] }})</h2>
                        </th>
                    </tr>
                    <tr>
                        <th>Bulan</th>
                        <th>Total Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesData['labels'] as $index => $month)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ number_format($salesData['data'][$index], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Daycomp E-Commerce | Laporan Dibuat Otomatis</p>
        </div>
    </div>
</body>

</html>