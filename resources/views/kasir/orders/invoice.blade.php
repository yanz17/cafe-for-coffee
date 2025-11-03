<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->nomor_pesanan }}</title>
    {{-- Gunakan Tailwind CSS atau style minimalis untuk cetak --}}
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet"> 
    <style>
        @media print {
            body { font-size: 10pt; }
            .no-print { display: none; }
            .container { width: 80mm !important; margin: 0 auto; padding: 0 !important; }
            .max-w-xl { max-width: 80mm !important; }
        }
        .invoice-box {
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 14px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .total-line { font-size: 16px; font-weight: bold; border-top: 1px dashed #aaa; padding-top: 5px; margin-top: 5px;}
    </style>
</head>
<body class="bg-white">

<div class="invoice-box container max-w-xl text-xs sm:text-sm mx-auto p-4">
    
    <div class="text-center mb-4">
        <h1 class="text-xl font-extrabold text-gray-900">FOR COFFEE POS</h1>
        <p class="text-xs">Jl. Kenangan No. 123, Kota Bahagia</p>
        <p class="text-xs">Telp: 0812-3456-7890</p>
    </div>

    <div class="border-t border-b border-dashed border-gray-400 py-2 mb-4">
        <p>No. Pesanan: <span class="font-bold">{{ $order->nomor_pesanan }}</span></p>
        <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        <p>Kasir: {{ Auth::user()->name ?? 'System' }}</p>
        <p>Pelanggan: {{ $order->user->name ?? 'Walk-In Customer' }}</p>
        <p>Tipe: {{ ucfirst(str_replace('_', ' ', $order->tipe_pemesanan)) }} @if($order->meja) (Meja {{ $order->meja }}) @endif</p>
    </div>

    <table class="w-full text-xs">
        <thead>
            <tr class="border-b border-gray-300">
                <th class="py-1 text-left">Item</th>
                <th class="py-1 text-center">Qty</th>
                <th class="py-1 text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td class="py-1 text-left">{{ $item->menu->nama }}</td>
                <td class="py-1 text-center">{{ $item->kuantitas }}</td>
                <td class="py-1 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-line text-right mt-3 pt-3">
        <p>TOTAL: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
        <p>Bayar: Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</p>
        <p>Kembali: Rp {{ number_format($order->change_due, 0, ',', '.') }}</p>
    </div>
    
    <div class="text-center mt-6">
        <p class="text-xs">Metode: {{ $order->payment_method_final ?? 'CASH' }}</p>
        <p class="font-semibold mt-1">Terima kasih atas kunjungannya!</p>
    </div>

</div>

{{-- Tombol Cetak (Hanya Muncul di Layar) --}}
<div class="text-center mt-6 no-print">
    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-lg shadow-md">
        Cetak Struk
    </button>
</div>

</body>
</html>