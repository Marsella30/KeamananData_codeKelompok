<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

class HunterController extends Controller
{
    public function index()
    {
        // Pastikan sudah login dengan guard 'pegawai'
        $pegawai = Auth::user();

        // Tambahkan validasi untuk role 'hunter'
        if ($pegawai && $pegawai->jabatan && strtolower($pegawai->jabatan->nama_jabatan) === 'hunter') {
            return response()->json([
                'status' => true,
                'message' => 'Data hunter berhasil diambil.',
                'data' => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'email' => $pegawai->email,
                    'notelp' => $pegawai->notelp,
                    'tanggal_lahir' => $pegawai->tanggal_lahir,
                    'jabatan' => $pegawai->jabatan->nama_jabatan
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai bukan hunter atau belum login.'
            ], 403);
        }
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

    public function getTotalKomisiHunter()
    {
        $pegawai = Auth::user();

        if ($pegawai && $pegawai->jabatan && strtolower($pegawai->jabatan->nama_jabatan) === 'hunter') {
            $totalKomisi = \App\Models\Komisi::where('id_pegawai', $pegawai->id_pegawai)
                                ->sum('komisi_hunter');

            return response()->json([
                'success' => true,
                'message' => 'Total komisi hunter berhasil dihitung.',
                'data' => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'total_komisi_hunter' => $totalKomisi
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai bukan hunter atau belum login.'
            ], 403);
        }
    }

    public function historyKomisi(Request $request)
    {
        $hunter = $request->user();  // Otentikasi pakai Sanctum

        $komisiList = \App\Models\Komisi::with(['barang', 'transaksi'])
            ->where('id_pegawai', $hunter->id_pegawai)
            ->orderByDesc('id_komisi')
            ->get()
            ->map(function ($komisi) {
                return [
                    'nama_barang' => $komisi->barang->nama_barang ?? '-',
                    'tanggal_transaksi' => optional($komisi->transaksi)->tanggal_transaksi 
                                        ? \Carbon\Carbon::parse($komisi->transaksi->tanggal_transaksi)->format('d/m/Y H:i')
                                        : '-',
                    'komisi_barang' => $komisi->komisi_hunter ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $komisiList
        ]);
    }

    public function historyKomisiLiveCode(Request $request)
    {
        $hunter = $request->user(); 

        $month = $request->input('month', null); 
        $year = $request->input('year', null);   

        $query = \App\Models\Komisi::with(['barang', 'transaksi'])
            ->where('id_pegawai', $hunter->id_pegawai)
            ->orderByDesc('id_komisi');


        if ($month && $year) {
            $query->whereHas('transaksi', function ($query) use ($month, $year) {
                $query->whereMonth('tanggal_transaksi', $month)
                    ->whereYear('tanggal_transaksi', $year);
            });
        }

        $komisiList = $query->get()
            ->map(function ($komisi) {
                return [
                    'nama_barang' => $komisi->barang->nama_barang ?? '-',
                    'tanggal_transaksi' => optional($komisi->transaksi)->tanggal_transaksi 
                                        ? \Carbon\Carbon::parse($komisi->transaksi->tanggal_transaksi)->format('d/m/Y H:i')
                                        : '-',
                    'komisi_barang' => $komisi->komisi_hunter ?? 0,
                ];
            });


        return response()->json([
            'success' => true,
            'data' => $komisiList
        ]);
    }


}
