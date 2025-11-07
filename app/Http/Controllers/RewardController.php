<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use App\Models\Merchandise;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'semua');
        $search = $request->query('search');
        $tanggalKlaim = $request->query('tanggal_klaim');
        // $tanggalAkhir = $request->query('tanggal_akhir');

        $query = Reward::with(['pembeli', 'merchandise'])
            ->where('jenis_reward', 'Merchandise');

        if ($filter === 'belum') {
            $query->where('status_penukaran', false);
        }

        if($search){
            $query->where(function ($q) use ($search) {
                $q->where('id_reward', 'like', "%$search%")
                ->orWhere('jumlah_tukar_poin', 'like', "%$search%")
                ->orWhereHas('pembeli', function ($q) use ($search) {
                    $q->where('nama_pembeli', 'like', "%$search%");
                })
                ->orWhereHas('merchandise', function ($q) use ($search) {
                    $q->where('nama_merchandise', 'like', "%$search%");
                });
            });
        }

        if ($tanggalKlaim) {
            $query->whereDate('tanggal_klaim', $tanggalKlaim);
        }

        $rewards = $query->orderBy('tanggal_klaim', 'desc')->paginate(10);

        return view('cs.reward.index', compact('rewards', 'filter', 'search', 'tanggalKlaim'));
    }

    public function ambilMerch($id)
    {
        $reward = Reward::findOrFail($id);

        if ($reward->jenis_reward !== 'Merchandise') {
            return redirect()->back()->with('error', 'Bukan klaim merchandise.');
        }

        $reward->status_penukaran = true;
        $reward->tanggal_ambil = now()->toDateString();
        $reward->save();

        return redirect()->route('cs.reward.index')->with('success', 'Merchandise telah diambil.');
    }

    public function claimMerchandise(Request $request)
    {
        try {
            $request->validate([
                'id_pembeli' => 'required|integer|exists:pembeli,id_pembeli',
                'id_merchandise' => 'required|integer|exists:merchandise,id_merchandise',
            ]);

            // Ambil merchandise
            $merchandise = Merchandise::findOrFail($request->id_merchandise);

            // Cek stok merchandise
            if ($merchandise->banyak_merchandise <= 0) {
                return response()->json(['success' => false, 'error' => 'Stok merchandise habis.'], 400);
            }

            // Ambil pembeli
            $pembeli = \App\Models\Pembeli::findOrFail($request->id_pembeli);

            // Cek poin pembeli cukup atau tidak
            if ($pembeli->poin < $merchandise->jumlah_poin) {
                return response()->json(['success' => false, 'error' => 'Poin Anda tidak mencukupi untuk klaim merchandise ini.'], 400);
            }

            // Simpan reward
            $reward = new Reward();
            $reward->id_pembeli = $request->id_pembeli;
            $reward->id_merchandise = $request->id_merchandise;
            $reward->jumlah_tukar_poin = $merchandise->jumlah_poin;
            $reward->tanggal_klaim = now();
            $reward->tanggal_ambil = null;
            $reward->status_penukaran = false;
            $reward->jenis_reward = 'Merchandise';
            $reward->save();

            // Update stok merchandise
            $merchandise->banyak_merchandise -= 1;
            $merchandise->save();

            // Update poin pembeli
            $pembeli->poin -= $merchandise->jumlah_poin;
            $pembeli->save();

            return response()->json(['success' => true, 'message' => 'Klaim berhasil']);
        } catch (\Exception $e) {
            \Log::error('Gagal klaim merchandise: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan saat klaim merchandise.'], 500);
        }
    }

    public function history($id_pembeli)
    {
        try {
            $pembeli = Pembeli::findOrFail($id_pembeli);

            $history = Reward::where('id_pembeli', $id_pembeli)
                ->where('jenis_reward', 'Merchandise')
                ->with('merchandise')
                ->orderBy('tanggal_klaim', 'desc')
                ->get()
                ->map(function($reward) {
                    return [
                        'id_reward' => $reward->id_reward,
                        'nama_merchandise' => $reward->merchandise->nama_merchandise ?? '-',
                        'gambar_url' => $reward->merchandise ? url('storage/merchandise/' . $reward->merchandise->nama_merchandise . '.jpg') : null,
                        'jumlah_tukar_poin' => $reward->jumlah_tukar_poin,
                        'tanggal_klaim' => $reward->tanggal_klaim ? $reward->tanggal_klaim->format('d/m/Y') : '-',
                        'tanggal_ambil' => $reward->tanggal_ambil ? $reward->tanggal_ambil->format('d/m/Y') : '-',
                        'status_penukaran' => $reward->status_penukaran ? 'Selesai' : 'Belum Diambil'
                    ];
                });

            return response()->json(['success' => true, 'data' => $history]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil history reward: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Gagal mengambil history reward.'], 500);
        }
    }
}
