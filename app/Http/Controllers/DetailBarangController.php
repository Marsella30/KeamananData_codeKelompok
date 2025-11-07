<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\BarangTitipan;

class DetailBarangController extends Controller
{
    public function index()
    {
        return view('detailBarang');
    }

    // public function show($id)
    // {
    //     // Ambil produk berdasarkan ID (misalnya dari model BarangTitipan)
    //     $product = BarangTitipan::find($id);
        
    //     if (!$product) {
    //         abort(404, "Produk tidak ditemukan.");
    //     }
        
    //     // Memeriksa status garansi
    //     $garansi_status = $this->checkWarrantyStatus($product->tanggal_garansi);
        
    //     // Mengirim data ke view
    //     return view('detailBarang', compact('product', 'garansi_status'));
    // }

    public function show($id)
    {
        $product = BarangTitipan::with(['kategori', 'fotoBarang','penitip'])->findOrFail($id);

        $idPenitip = $product->id_penitip;
        $avgRating = \App\Models\Rating::where('id_penitip', $idPenitip)->avg('rating');

        if (is_null($avgRating)) {
            $avgRating = 0;
        }
        
        if (!$product) {
            abort(404, "Produk tidak ditemukan.");
        }
        
        $garansi_status = $this->checkWarrantyStatus($product->tanggal_garansi);

        $produk_serupa = BarangTitipan::with('fotoBarang')
            ->where('id_kategori', $product->id_kategori)
            ->where('id_barang', '!=', $id)
            ->where('status_barang', 'Tersedia')
            ->take(4)
            ->get();

        return view('detailBarang', compact('product', 'garansi_status', 'produk_serupa', 'avgRating', 'idPenitip'));
    }

    private function checkWarrantyStatus($tanggal_garansi)
    {
        if (!$tanggal_garansi) {
            return "Garansi Tidak Tersedia"; 
        }

        $garansi = Carbon::parse($tanggal_garansi);
        $today = Carbon::now();

        if ($garansi->isFuture()) {
            return "Garansi Masih Berlaku";
        } else {
            return "Garansi Sudah Berakhir";
        }
    }

}
