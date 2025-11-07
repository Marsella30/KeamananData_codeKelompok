<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DiskusiProduk;
use App\Models\BarangTitipan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DiskusiController extends Controller
{
    // Menampilkan halaman diskusi untuk suatu barang
    public function index()
    {
        // Ambil semua diskusi dan data terkait
        $diskusi = DiskusiProduk::with(['barang_titipan', 'pegawai', 'pembeli'])
        ->orderBy('tanggal_tanya', 'desc')
        ->paginate(5);

        // Ambil barang yang statusnya tersedia untuk dropdown
        $barangAvailable = BarangTitipan::where('status_barang', 'Tersedia')->get();

        return view('diskusi', compact('diskusi', 'barangAvailable'));
    }

    public function storePertanyaan(Request $request)
    {
        // Validasi inputan
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang_titipan,id_barang',
            'pertanyaan' => 'required|string|max:255',
        ]);

        // Menyimpan pertanyaan diskusi baru
        $diskusi = new DiskusiProduk();
        $diskusi->id_barang = $validated['barang_id'];
        $diskusi->id_pembeli = auth()->user()->id_pembeli; // Pastikan pengguna sudah login dan memiliki ID pembeli
        $diskusi->pertanyaan = $validated['pertanyaan'];
        $diskusi->tanggal_tanya = now();
        $diskusi->save();

        // Redirect kembali ke halaman diskusi
        return redirect()->route('diskusi.index')->with('success', 'Pertanyaan berhasil diajukan.');
    }

    // Menjawab pertanyaan oleh pegawai (hanya jika belum dijawab)
    public function showBelumDijawab()
    {
        // Ambil semua diskusi yang belum dijawab (id_pegawai null)
        $diskusi = DiskusiProduk::with(['barang_titipan', 'pembeli'])
            ->whereNull('id_pegawai')  // Hanya yang belum dijawab
            ->orderBy('tanggal_tanya', 'desc')  // Urutkan berdasarkan tanggal_tanya terbaru
            ->paginate(5);  // Pagination

        return view('cs.diskusiIndex', compact('diskusi'));
    }

    // Fungsi untuk menyimpan jawaban
    public function jawab(Request $request, $id)
{
    // Validasi jawaban
    $validated = $request->validate([
        'jawaban' => 'required|string|max:255',
    ]);

    // Temukan data diskusi berdasarkan ID
    $diskusi = DiskusiProduk::findOrFail($id);
    $diskusi->jawaban = $validated['jawaban'];
    $diskusi->tanggal_jawab = now();
    $diskusi->id_pegawai = auth()->user()->id_pegawai;  // Menyimpan id_pegawai yang menjawab
    $diskusi->save();

    // Mengembalikan response JSON dengan data yang dibutuhkan
    return response()->json([
        'success' => true,
        'message' => 'Pertanyaan berhasil dijawab.',
        'diskusi_id' => $diskusi->id_diskusi,
        'jawaban' => $diskusi->jawaban
    ]);
}

}
