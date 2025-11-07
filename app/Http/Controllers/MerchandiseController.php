<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Merchandise;

class MerchandiseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $merchandise = Merchandise::select(
                    'id_merchandise',
                    'nama_merchandise',
                    'jumlah_poin',
                    'banyak_merchandise'
                )
                ->orderBy('id_merchandise')
                ->get();

            foreach ($merchandise as $item) {
                // Membuat URL gambar dari nama_merchandise (pastikan format nama file sesuai)
                $namaGambar = str_replace(' ', '_', strtolower($item->nama_merchandise)) . '.jpg';
                $item->gambar_url = asset('images/merchandise/' . $namaGambar);
            }

            return response()->json([
                'success' => true,
                'data' => $merchandise
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching merchandise: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data merchandise.'
            ], 500);
        }
    }


}
