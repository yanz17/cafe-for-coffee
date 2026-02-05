<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SalesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * Asumsi kolom Excel Anda: nomor_pesanan, nama_menu, kuantitas, total_harga, metode_bayar, tanggal
     */
    public function model(array $row)
    {
        // 1. Cari menu berdasarkan nama
        $menu = Menu::where('nama', 'like', '%' . ($row['nama_menu'] ?? '') . '%')->first();

        if (!$menu) {
            return null; // Lewati jika menu tidak ditemukan
        }

        // 2. Parsing Tanggal (Handle format Excel Serial vs String)
        $tanggal = now();
        if (!empty($row['tanggal'])) {
            try {
                // Jika Excel mengirimkan angka (serial date), konversi menggunakan library Spreadsheet
                if (is_numeric($row['tanggal'])) {
                    $tanggal = Carbon::instance(Date::excelToDateTimeObject($row['tanggal']));
                } else {
                    $tanggal = Carbon::parse($row['tanggal']);
                }
            } catch (\Exception $e) {
                $tanggal = now(); // Fallback jika format hancur
            }
        }

        // 3. Simpan ke tabel Orders (Header)
        // Kita tidak menggunakan Order::create() agar bisa menset created_at secara manual
        $order = new Order();
        $order->nomor_pesanan = $row['nomor_pesanan'] ?? 'IMP-' . Str::upper(Str::random(5));
        $order->total_harga = $row['total_harga'] ?? ($row['kuantitas'] * $menu->harga);
        $order->status_pesanan = 'selesai';
        $order->status_pembayaran = 'lunas';
        $order->tipe_pemesanan = 'take_away';
        $order->payment_method_final = $row['metode_bayar'] ?? 'Cash';
        
        // Kunci Perbaikan: Set created_at dan updated_at secara manual
        $order->created_at = $tanggal;
        $order->updated_at = $tanggal;
        $order->save();

        // 4. Simpan ke tabel OrderItems (Detail)
        return new OrderItem([
            'order_id'     => $order->id,
            'menu_id'      => $menu->id,
            'kuantitas'    => $row['kuantitas'],
            'harga_satuan' => $menu->harga,
            'subtotal'     => $row['kuantitas'] * $menu->harga,
        ]);
    }
}