<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Dikirim</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .logo {
            display: block;
            max-width: 150px;
            margin: 0 auto 20px auto;
        }
        h3 {
            color: #333333;
            margin-top: 0;
        }
        p {
            font-size: 15px;
            color: #555555;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #f0f4f8;
            margin: 8px 0;
            padding: 12px;
            border-radius: 5px;
            color: #333333;
        }
        li strong {
            display: inline-block;
            width: 150px;
            font-weight: 600;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #888888;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Tambahkan logo di sini -->
         <img src="https://ik.imagekit.io/jh93hgpo3/logo2.png?updatedAt=1748623080633" alt="ReUseMart Logo" class="logo">

        <h3>Halo,</h3>
        <p>Berikut adalah detail jadwal pengiriman/pengambilan untuk transaksi nomor: <span class="highlight">#{{ $transaksi->nomor_transaksi }}</span></p>

        <ul>
            <li><strong>Jenis Jadwal:</strong> {{ $jadwal->jenis_jadwal }}</li>
            <li><strong>Tanggal Jadwal:</strong> {{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y H:i') }}</li>
            <li><strong>Status Jadwal:</strong> {{ $jadwal->status_jadwal }}</li>
        </ul>

        <p>Terima kasih telah menggunakan layanan kami.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} ReUseMart
    </div>
</body>
</html>
