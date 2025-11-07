<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangTitipan;
use App\Models\Keranjang;
use App\Models\DetailKeranjang;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function addToCart(Request $request)
    {
        $idBarang = $request->input('id_barang');
        $idPembeli = Auth::guard('pembeli')->id(); // asumsikan guard pembeli

        // Cari atau buat keranjang milik pembeli
        $keranjang = Keranjang::firstOrCreate(
            ['id_pembeli' => $idPembeli]
        );

        // Cek apakah barang sudah ada dalam keranjang
        $exists = DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)
                    ->where('id_barang', $idBarang)
                    ->exists();

        if ($exists) {
            return back()->with('warning', 'Barang sudah ada di keranjang.');
        }

        // Tambahkan ke detail keranjang
        DetailKeranjang::create([
            'id_keranjang' => $keranjang->id_keranjang,
            'id_barang' => $idBarang
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan ke keranjang.');
    }

    public function showCart()
    {
        $idPembeli = Auth::guard('pembeli')->id();

        // Ambil semua id_keranjang milik pembeli
        $keranjangIds = Keranjang::where('id_pembeli', $idPembeli)->pluck('id_keranjang');

        // Ambil semua barang dari semua keranjang pembeli tersebut
        $barang = BarangTitipan::join('detail_keranjang', 'barang_titipan.id_barang', '=', 'detail_keranjang.id_barang')
            ->whereIn('detail_keranjang.id_keranjang', $keranjangIds)
            ->select('barang_titipan.id_barang', 'barang_titipan.nama_barang', 'barang_titipan.deskripsi', 'barang_titipan.harga_jual')
            ->get();


        return view('keranjang', ['items' => $barang]);
    }


    public function removeFromCart($id_barang)
    {
        $idPembeli = Auth::guard('pembeli')->id();

        $keranjang = Keranjang::where('id_pembeli', $idPembeli)->first();
        if (!$keranjang) {
            return back()->with('error', 'Keranjang tidak ditemukan.');
        }

        DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)
                    ->where('id_barang', $id_barang)
                    ->delete();

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }
}
