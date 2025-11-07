<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\BarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;

class PenitipController extends Controller
{
    public function profilePenitip()
    {
        $penitip = auth()->guard('penitip')->user(); // pastikan guard 'penitip'

        $transaksiList = BarangTitipan::with('penitip')
            ->where('id_penitip', $penitip->id_penitip)
            ->where('status_barang', 'terjual')
            ->orderByDesc('tanggal_keluar')
            ->get();
            
        $avgRating = Rating::where('id_penitip', $penitip->id_penitip)->avg('rating');

        if (is_null($avgRating)) {
            $avgRating = 0;
        }

        $countRating = Rating::where('id_penitip', $penitip->id_penitip)->count();

        return view('Penitip.profilePenitip', compact('penitip', 'transaksiList', 'avgRating', 'countRating'));
    }

    public function tarikSaldo(Request $request)
    {
        $penitip = auth()->guard('penitip')->user();
        // $request = $penitip->nominal_tarik;
        
        // if ($penitip->saldo_penitip > $penitip->nominal_tarik) {
        //     $penitip->saldo_penitip -= $penitip->nominal_tarik;
        //     $penitip->save();
        // }

        return view('Penitip.PengajuanPenarikanSaldo', compact('penitip'));
    }

    public function prosesTarikSaldo(Request $request)
    {
        $request->validate([
            'nominal_tarik' => 'required|numeric|min:1',
        ]);

        $penitip = auth()->guard('penitip')->user();
        $nominalTarik = $request->nominal_tarik;
        $biaya = $nominalTarik * 0.05;
        $totalPotongan = $nominalTarik + $biaya;

        if ($penitip->saldo_penitip < $totalPotongan) {
            return back()->withErrors(['nominal_tarik' => 'Saldo tidak mencukupi untuk penarikan ini.']);
        }

        // Jika cukup, lanjut proses
        $penitip->saldo_penitip -= $totalPotongan;
        $penitip->save();

        return redirect()->back()->with('success', 'Penarikan berhasil.');
    }

    public function apiProfilePenitip()
    {
        try {
            $penitip = auth('sanctum')->user();

            if (!$penitip) {
                return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
            }

            // Hitung rating rata-rata
            $avgRating = Rating::where('id_penitip', $penitip->id_penitip)->avg('rating') ?? 0;

            // Hitung jumlah rating
            $countRating = Rating::where('id_penitip', $penitip->id_penitip)->count();

            $penitipData = [
                'id_penitip'    => $penitip->id_penitip,
                'poin'          => $penitip->poin,
                'nama_penitip'  => $penitip->nama_penitip,
                'email'         => $penitip->email,
                'no_telp'       => $penitip->no_telp,
                'alamat'        => $penitip->alamat,
                'saldo_penitip' => $penitip->saldo_penitip,
                'no_ktp'        => $penitip->no_ktp,
                'username'      => $penitip->username,
                'password'      => $penitip->password,
                'foto_ktp'      => $penitip->foto_ktp,
                'status_aktif'  => $penitip->status_aktif,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'penitip'       => $penitipData,
                    // 'transaksiList' => $transaksiList,
                    'avgRating'     => round($avgRating, 2),
                    'countRating'   => $countRating,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiRiwayatTitipan()
    {
        try {
            $penitip = auth('sanctum')->user();

            if (!$penitip) {
                return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
            }

            $riwayat = BarangTitipan::where('id_penitip', $penitip->id_penitip)
                ->orderByDesc('tanggal_masuk')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $riwayat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $search = $request->input('q');

        $penitips = Penitip::when($search, function ($query, $search) {
            return $query->where('nama_penitip', 'like', "%$search%")
                        ->orWhere('no_ktp', 'like', "%$search%")->orWhere('username', 'like', "%$search%")->orWhere('email', 'like', "%$search%");
        })->paginate(10);

        return view('CS.penitipIndex', [
            'penitips' => $penitips,
            'search' => $search
        ]);        
    }

    public function create()
    {
        return view('cs.penitip.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'no_ktp.unique' => 'No KTP sudah terdaftar. Silakan gunakan yang lain.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan yang lain.',
            'username.unique' => 'Username sudah terdaftar. Silakan pilih yang lain.',
        ];

        $request->validate([
            'no_ktp' => 'required|unique:penitip,no_ktp',
            'foto_ktp' => 'nullable|image|max:2048',
            'nama_penitip' => 'required',
            'alamat' => 'required',
            'email' => 'required|email|unique:penitip,email',
            'username' => 'required|unique:penitip,username',
            'password' => 'required|min:6',
        ], $messages); 

        $data = $request->all();

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'public');
        }

        $data['poin'] = 0;
        $data['saldo_penitip'] = 0;
        $data['status_aktif'] = 1;

        Penitip::create($data);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $penitip = Penitip::findOrFail($id);
        return view('cs.penitip.edit', compact('penitip'));
    }

    public function update(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);
        $messages = [
            'no_ktp.unique' => 'No KTP sudah terdaftar. Silakan gunakan yang lain.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan yang lain.',
            'username.unique' => 'Username sudah terdaftar. Silakan pilih yang lain.',
        ];

        $request->validate([
            'no_ktp' => 'required|unique:penitip,no_ktp,' . $id . ',id_penitip',
            'foto_ktp' => 'nullable|image|max:2048',
            'nama_penitip' => 'required',
            'email' => 'required|email|unique:penitip,email,' . $id . ',id_penitip',
            'username' => 'required|unique:penitip,username,' . $id . ',id_penitip',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto_ktp')) {
            if ($penitip->foto_ktp) {
                Storage::disk('public')->delete($penitip->foto_ktp);
            }
            $data['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'public');
        }

        $penitip->update($data);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penitip = Penitip::findOrFail($id);
        if ($penitip->foto_ktp) {
            Storage::disk('public')->delete($penitip->foto_ktp);
        }
        $penitip->delete();
        return redirect()->route('cs.penitip.index')->with('success', 'Penitip berhasil dihapus.');
    }

    public function showRating($id_penitip)
    {
        $avgRating = Rating::where('id_penitip', $id_penitip)
            ->avg('rating');

        if (is_null($avgRating)) {
            $avgRating = 0;
        }

        return view('penitip.rating', compact('id_penitip', 'avgRating'));
    }

    public function showM(Request $request)
    {
        // Ambil pengguna penitip yang sedang login
        $penitip = Auth::guard('penitip')->user();

        if (!$penitip) {
            return response()->json(['error' => 'Penitip tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'penitip' => $penitip
        ]);
    }

    public function dashboard(Request $request)
    {
        $penitip = auth()->guard('penitip')->user();

        $query = BarangTitipan::where('id_penitip', $penitip->id_penitip);

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $barangs = $query->orderByDesc('tanggal_masuk')->paginate(10);

        return view('penitip.dashboard', compact('barangs'));
    }

    public function apiDashboard(Request $request)
    {
        $penitip = auth('sanctum')->user();

        if (!$penitip) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $query = BarangTitipan::with(['kategori', 'fotoBarang']) // ← Tambahkan ini
            ->where('id_penitip', $penitip->id_penitip);

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && strtolower($request->status) != 'semua') {
            $query->where('status_barang', $request->status);
        }

        $barangs = $query->orderByDesc('tanggal_masuk')->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $barangs->items(),
            'pagination' => [
                'current_page' => $barangs->currentPage(),
                'last_page' => $barangs->lastPage(),
                'per_page' => $barangs->perPage(),
                'total' => $barangs->total(),
            ],
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

    

}