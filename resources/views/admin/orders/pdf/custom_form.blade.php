<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Formulir Kustom - {{ $order->resi_code }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        h3 {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Formulir Kustom</h2>
    <p><strong>Resi:</strong> {{ $order->resi_code }}</p>
    <p><strong>Nama:</strong> {{ $order->name }}</p>

    @foreach ($order->orderDetails as $item)
        @if (!empty($item->custom_form_data_labelled))
            <h3>{{ $item->product->product_name ?? 'Produk' }}</h3>
            <ul>
                @foreach ($item->custom_form_data_labelled as $label => $value)
                    <li><strong>{{ $label }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
        @endif
    @endforeach
</body>

</html>