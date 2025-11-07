<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangTitipan;

class HomeController extends Controller
{
    // public function index()
    // {
    //     return view('home');
    // }

    public function index()
    {
        $barangs = BarangTitipan::with(['kategori', 'fotoBarang'])->where('status_barang', 'Tersedia')->get();
        return view('home', compact('barangs'));
    }

    public function apiIndex()
    {
        $barangs = BarangTitipan::with(['kategori','fotoBarang'])
            ->where('status_barang','Tersedia')
            ->get();

        return response()->json([
            'data' => $barangs
        ]);
    }

    public function byCategory($categoryId)
    {
        $barangs = BarangTitipan::with(['kategori','fotoBarang'])
            ->where('status_barang','Tersedia')
            ->where('id_kategori',$categoryId)
            ->get();

        return response()->json([
            'data' => $barangs
        ]);
    }
    
    public function showProducts()
    {
        // Mengambil semua produk barang titipan
        // $barangTitipan = BarangTitipan::all();
        $barangTitipan = BarangTitipan::with(['kategori', 'fotoBarang'])->get();

        // Mengirim data produk ke view
        return view('home', compact('barangTitipan'));
    }

    public function showProductsByCategory($categoryId)
    {
        // Mengambil produk berdasarkan kategori
        // $barangTitipan = BarangTitipan::where('id_kategori', $categoryId)->get();
        $barangTitipan = BarangTitipan::with(['kategori', 'fotoBarang'])
            ->where('id_kategori', $categoryId)
            ->get();

        // Mengirim data ke view
        return view('home', compact('barangTitipan'));
    }

}
