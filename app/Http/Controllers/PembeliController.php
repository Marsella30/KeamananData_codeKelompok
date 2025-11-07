<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Pembeli;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\BarangTitipan;
use App\Models\Penjadwalan;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PembeliController extends Controller
{
    public function profilePembeli()
    {
        $pembeli = auth()->guard('pembeli')->user();

        $transaksiList = Transaksi::with('detailTransaksi.barang', 'penjadwalans.pengiriman')
            ->where('id_pembeli', $pembeli->id_pembeli)
            ->whereHas('penjadwalans.pengiriman', function($q) {
                $q->where('status_pengiriman', 'Diterima');
            })
            ->orderByDesc('tanggal_transaksi')
            ->get();

        return view('profilePembeli', compact('pembeli', 'transaksiList'));
    }

    public function detailRiwayat()
    {
        
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pembeli' => 'required',
            'username'     => 'required|unique:pembeli,username,' . $id . ',id_pembeli',
            'notelp'       => 'required|unique:pembeli,notelp,' . $id . ',id_pembeli',
            'email'        => 'required|email|unique:pembeli,email,' . $id . ',id_pembeli',
        ]);

        $pembeli = Pembeli::findOrFail($id);
        $pembeli->update($request->all());

        return redirect()->route('pembeli.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $pembeli = \App\Models\Pembeli::findOrFail($id);

        \App\Models\Pembeli::where('id_pembeli', $id)->update([
            'status_aktif' => !$pembeli->status_aktif
        ]);

        return redirect()->back()->with('success', 'Status akun diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required',
            'username' =>'required',
            'notelp' =>'required',
            'email' =>'required',
            'password'=>'required',
        ]);

        Pembeli::create([
            'nama_pembeli' => $request->nama_pembeli,
            'username' => $request->username,
            'notelp' => $request->notelp,
            'email' => $request->email,
            'password' => $request->password,
            'poin' => 0,
            'status_aktif' => 1,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Pembeli berhasil dibuat.');
    }

    public function riwayatTransaksi(Request $request)
    {
        $pembeli = auth()->guard('pembeli')->user();
        $userId = $pembeli->id_pembeli;
        $search = $request->input('search');

        $query = Transaksi::with([
            'detailTransaksi.barang.ratingDetail',
            'penjadwalans.pengiriman'
        ])->where('id_pembeli', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                ->orWhere('status_transaksi', 'like', "%{$search}%")
                ->orWhereHas('detailTransaksi.barang', function ($q2) use ($search) {
                    $q2->where('nama_barang', 'like', "%{$search}%");
                })
                ->orWhereHas('penjadwalans.pengiriman', function ($q3) use ($search) {
                    $q3->where('status_pengiriman', 'like', "%{$search}%");
                });
            });
        }

        $transaksiList = $query->orderByDesc('tanggal_transaksi')->paginate(5);

        // Ambil status pengiriman untuk tiap transaksi
        foreach ($transaksiList as $transaksi) {
            $statusPengirimanText = 'Belum ada pengiriman';

            if ($transaksi->penjadwalans) {
                foreach ($transaksi->penjadwalans as $penjadwalan) {
                    if ($penjadwalan && $penjadwalan->pengiriman) {
                        $statusPengirimanText = strtolower(trim($penjadwalan->pengiriman->status_pengiriman));
                        if (in_array($statusPengirimanText, ['diterima', 'sampai'])) {
                            break;
                        }
                    }
                }
            }
            $transaksi->status_pengiriman = $statusPengirimanText;
        }

        return view('pembeli.riwayatTransaksi', compact('pembeli', 'transaksiList'));
    }

    public function beriRatingForm($id_barang)
    {
        $userId = auth()->guard('pembeli')->id();

        $barang = BarangTitipan::where('id_barang', $id_barang)
            ->whereHas('detail_transaksi.transaksi.penjadwalans.pengiriman', function ($query) {
                $query->whereIn(DB::raw('LOWER(status_pengiriman)'), ['Diterima', 'Sampai']);
            })
            ->whereHas('detail_transaksi.transaksi', function ($query) use ($userId) {
                $query->where('id_pembeli', $userId);
            })
            ->with('penitip')
            ->firstOrFail();

        $existing = Rating::where('id_barang', $id_barang)
            ->where('id_pembeli', $userId)
            ->first();

        return view('pembeli.tambahRating', compact('barang', 'existing'));
    }

    public function storeRating(Request $request)
    {
        $userId = auth()->guard('pembeli')->id();

        $request->validate([
            'id_barang' => 'required|exists:barang_titipan,id_barang',
            'rating'    => 'required|integer|min:1|max:5',
        ]);

        $barang = BarangTitipan::where('id_barang', $request->id_barang)
            ->whereHas('detail_transaksi.transaksi.penjadwalans.pengiriman', function ($query) {
                $query->whereIn(DB::raw('LOWER(status_pengiriman)'), ['Diterima', 'Sampai']);
            })
            ->whereHas('detail_transaksi.transaksi', function ($query) use ($userId) {
                $query->where('id_pembeli', $userId);
            })
            ->with('penitip')
            ->firstOrFail();

        Rating::updateOrCreate(
            [
                'id_barang' => $request->id_barang,
                'id_pembeli' => $userId,
            ],
            [
                'id_penitip' => $barang->id_penitip,
                'rating' => $request->rating,
            ]
        );

        return redirect()->route('pembeli.riwayatTransaksi')->with('success', 'Rating berhasil disimpan.');
    }

    public function showM(Request $request)
    {
        // Ambil pengguna yang sedang login (pastikan sudah login dengan Sanctum)
        $pembeli = Auth::guard('pembeli')->user();

        if (!$pembeli) {
            return response()->json(['error' => 'Pembeli tidak ditemukan.'], 404);
        }

        // Mengembalikan data pembeli sebagai response JSON
        return response()->json([
            'success' => true,
            'pembeli' => $pembeli
        ]);
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();

        // Simpan token FCM ke user (misal kolom 'fcm_token' di tabel users)
        // Jika kamu pakai tabel lain, sesuaikan

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['success' => true, 'message' => 'FCM token saved']);
    }

    public function profileMobile(Request $request)
    {
        $pembeli = Auth::guard('sanctum')->user();

        if (!$pembeli) {
            return response()->json([
                'success' => false,
                'error' => 'Pembeli tidak ditemukan atau belum login.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_pembeli' => $pembeli->id_pembeli,
                'nama_pembeli' => $pembeli->nama_pembeli,
                'username' => $pembeli->username,
                'email' => $pembeli->email,
                'notelp' => $pembeli->notelp,
                'poin' => $pembeli->poin,
                'status_aktif' => $pembeli->status_aktif,
            ],
        ]);
    }

    public function apiRiwayatTransaksi(Request $request)
    {
        $pembeli = auth()->user();

        if (!$pembeli) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized. Pembeli tidak ditemukan.'
            ], 401);
        }

        $userId = $pembeli->id_pembeli;
        $search = $request->input('search');

        $query = Transaksi::with([
            'detailTransaksi.barang.ratingDetail',
            'penjadwalans.pengiriman'
        ])->where('id_pembeli', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                ->orWhere('status_transaksi', 'like', "%{$search}%")
                ->orWhereHas('detailTransaksi.barang', function ($q2) use ($search) {
                    $q2->where('nama_barang', 'like', "%{$search}%");
                })
                ->orWhereHas('penjadwalans.pengiriman', function ($q3) use ($search) {
                    $q3->where('status_pengiriman', 'like', "%{$search}%");
                });
            });
        }

        $transaksiList = $query->orderByDesc('tanggal_transaksi')->paginate(5);

        // Ambil status pengiriman untuk tiap transaksi
        foreach ($transaksiList as $transaksi) {
            $statusPengirimanText = 'Belum ada pengiriman';

            if ($transaksi->penjadwalans) {
                foreach ($transaksi->penjadwalans as $penjadwalan) {
                    if ($penjadwalan && $penjadwalan->pengiriman) {
                        $statusPengirimanText = strtolower(trim($penjadwalan->pengiriman->status_pengiriman));
                        if (in_array($statusPengirimanText, ['diterima', 'sampai'])) {
                            break;
                        }
                    }
                }
            }

            $transaksi->status_pengiriman = $statusPengirimanText;
        }

        return response()->json([
            'success' => true,
            'data' => $transaksiList,
        ]);
    }
}
