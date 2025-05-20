<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
        }

        img {
            width: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 5rem;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1f2937;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        a:hover {
            background-color: #111827;
        }
    </style>
</head>

<body>
    <img src="{{ asset('dc.jpg') }}" alt="DayComp Logo">
    <h1>404</h1>
    <p>Maaf, halaman yang Anda cari tidak ditemukan.</p>
    <a href="{{ url('/') }}">Kembali ke Beranda</a>
</body>

</html>