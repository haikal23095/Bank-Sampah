<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi #{{ $transaction->id }} - Bank Sampah Migunani</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        .receipt-container {
            max-width: 350px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .meta-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-table td:first-child {
            width: 35%;
        }
        .meta-table td:nth-child(2) {
            width: 5%;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            font-size: 12px;
        }
        .items-table td {
            padding: 6px 0;
            font-size: 13px;
        }
        .align-right {
            text-align: right;
        }
        .total-section {
            margin-top: 15px;
            font-weight: bold;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
        }
        .no-print-btn {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #10b981;
            color: white;
            border: none;
            padding: 10px;
            font-family: sans-serif;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <!-- Print Button for manual click if needed -->
        <button class="no-print-btn" onclick="window.print()">Cetak Nota</button>

        <div class="header">
            <h1>BANK SAMPAH MIGUNANI</h1>
            <p>GPM BYPASS RW 04</p>
            <p>Sistem Informasi Bank Sampah</p>
        </div>

        <div class="separator"></div>

        <table class="meta-table">
            <tr>
                <td>No. Nota</td>
                <td>:</td>
                <td>#{{ $transaction->id }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Nasabah</td>
                <td>:</td>
                <td>{{ $transaction->nasabah->name }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td>:</td>
                <td>{{ $transaction->petugas->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Jenis Sampah</th>
                    <th style="width: 25%; text-align: center;">Berat</th>
                    <th style="width: 30%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalWeight = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($transaction->details as $detail)
                    @php
                        $totalWeight += $detail->weight;
                        $totalAmount += $detail->subtotal;
                    @endphp
                    <tr>
                        <td>
                            {{ $detail->wasteType->name }}<br>
                            <span style="font-size: 11px; color: #555;">@ Rp {{ number_format($detail->wasteType->price_per_kg ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td style="text-align: center;">{{ $detail->weight }} {{ $detail->wasteType->unit ?? 'kg' }}</td>
                        <td class="align-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <div class="total-section">
            <div class="total-row">
                <span>Total Berat</span>
                <span>{{ number_format($totalWeight, 2, ',', '.') }} kg</span>
            </div>
            <div class="total-row" style="font-size: 15px; margin-top: 4px;">
                <span>Total Saldo</span>
                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="separator"></div>

        <div class="footer">
            <p>Terima kasih telah menabung sampah dan menjaga lingkungan tetap bersih!</p>
            <p>--- Layanan Bank Sampah Migunani ---</p>
        </div>
    </div>

    <script>
        // Auto trigger print dialog when page finishes loading
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
