<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\BarangTitipan;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    // public function index()
    // {
    //     // Menampilkan semua kategori
    //     $categories = Kategori::all();
    //     return view('kategori.index', compact('categories'));
    // }
    public function index()
    {
        return view('kategori');
    }

    // public function showProducts()
    // {
    //     // Mengambil semua produk barang titipan
    //     $barangTitipan = BarangTitipan::all();

    //     // Mengirim data produk ke view
    //     return view('kategori', compact('barangTitipan'));
    // }

    public function showAvailableProducts()
    {
        $produk = BarangTitipan::with(['kategori', 'fotoBarang'])->where('status_barang', 'Tersedia')->get();
        $kategori = null;
        return view('kategori', compact('produk', 'kategori'));
    }

    // public function showProductsByCategory($id)
    // {
    //     $kategori = Kategori::findOrFail($id);
    //     $produk = BarangTitipan::with('kategori')
    //         ->where('id_kategori', $id)
    //         ->where('status_barang', 'Tersedia')
    //         ->get();

    //     return view('kategori', compact('kategori', 'produk'));
    // }
    // KategoriController.php

    public function indexKategori()
    {
        try {
            $categories = Kategori::all();  // Mengambil semua kategori
            
            // Log jika kategori berhasil diambil
            Log::info('Kategori berhasil diambil.', ['categories' => $categories]);

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            // Log jika terjadi error
            Log::error('Gagal mengambil kategori.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil kategori.',
            ], 500);
        }
    }
    
    public function apiIndex()
    {
        $kats = Kategori::all();
        return response()->json(['data' => $kats]);
    }

    public function apiProductsByCategory($id)
    {
        $kategori = Kategori::findOrFail($id);

        $produk = BarangTitipan::with(['kategori','fotoBarang'])
            ->where('id_kategori', $id)
            ->where('status_barang','Tersedia')
            ->get();

        return response()->json([
            'kategori' => $kategori,
            'data'     => $produk
        ]);
    }

    public function apiAllProducts()
    {
        $produk = BarangTitipan::with(['kategori','fotoBarang'])
            ->where('status_barang','Tersedia')
            ->get();

        return response()->json([
            'data' => $produk
        ]);
    }

    public function apiSearch(Request $request)
    {
        $q = $request->query('query','');
        $produk = BarangTitipan::with(['kategori','fotoBarang'])
            ->when($q, fn($qb) => $qb->where('nama_barang','like',"%{$q}%"))
            ->get();

        return response()->json([
            'data' => $produk
        ]);
    }

    public function showProductsByCategory($id)
    {
        // Ambil kategori berdasarkan ID
        $kategori = Kategori::findOrFail($id);
        
        // Ambil produk berdasarkan kategori
        $produk = BarangTitipan::with(['kategori', 'fotoBarang'])
            ->where('id_kategori', $id)
            ->where('status_barang', 'Tersedia')
            ->get();
        return view('kategori', compact('kategori', 'produk'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $products = BarangTitipan::with(['kategori', 'fotoBarang']) // ← ini penting
                ->where('nama_barang', 'like', '%' . $query . '%')
                ->get();
        } else {
            $products = BarangTitipan::with(['kategori', 'fotoBarang'])->get(); // ← juga di sini
        }

        return response()->json($products);
    }

    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
        $barang = BarangTitipan::with('fotoBarang')->where('id_kategori', $id)->get();

        return view('kategori', compact('kategori', 'barang'));
    }
}