<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Sinkronisasi - #{{ $report['id'] }}</title>
    <style>
        /* Using a basic font stack for PDF compatibility */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #374151;
            /* text-gray-700 */
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            /* border-gray-200 */
        }

        .company-info {
            text-align: right;
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

        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1e40af;
            /* text-blue-800 */
            text-align: center;
        }

        .summary-box {
            background-color: #f9fafb;
            /* bg-gray-50 */
            border: 1px solid #e5e7eb;
            /* border-gray-200 */
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        .summary-box p {
            margin: 0 0 6px 0;
            line-height: 1.6;
        }

        .summary-box strong {
            color: #4b5563;
            /* text-gray-600 */
            min-width: 150px;
            display: inline-block;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            /* text-gray-900 */
            margin-top: 25px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
            /* border-gray-300 */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            /* border-gray-200 */
            padding: 10px 12px;
            text-align: left;
        }

        thead {
            background-color: #f3f4f6;
            /* bg-gray-100 */
        }

        th {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .no-data {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            /* text-gray-500 */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <strong>Daycomp</strong>
                Laporan Sinkronisasi Gudang
            </div>
        </div>

        <h1 class="report-title">Laporan Sinkronisasi #{{ $report['id'] }}</h1>

        <div class="summary-box">
            <p><strong>Tanggal Sinkronisasi:</strong>
                {{ \Carbon\Carbon::parse($report['created_at'])->format('d F Y, H:i:s') }}</p>
            <p><strong>Total Produk Ditemukan:</strong> {{ $report['fetched_count'] }}</p>
            <p><strong>Produk Baru Ditambahkan:</strong> {{ $report['added_count'] }}</p>
            <p><strong>Produk Dilewati:</strong> {{ $report['skipped_count'] }}</p>
            <p><strong>Stok Diperbarui:</strong> {{ $report['stock_updated_count'] }}</p>
            <p><strong>Produk Dihapus:</strong> {{ $report['deleted_count'] }}</p>
        </div>

        @if(!empty($report['details']['new_products']))
            <h2 class="section-title">Detail Produk Baru</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kode</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['details']['new_products'] as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['code'] }}</td>
                            <td>{{ $product['category'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(!empty($report['details']['deleted_products']))
            <h2 class="section-title">Detail Produk Dihapus</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['details']['deleted_products'] as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['code'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(empty($report['details']['new_products']) && empty($report['details']['deleted_products']))
            <p class="no-data">Tidak ada perubahan detail produk pada sinkronisasi ini.</p>
        @endif
    </div>
</body>

</html>