<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    // Menampilkan daftar pegawai
    public function index()
    {
        $pegawai = Pegawai::with('jabatan')->get();
        return view('pegawai.index', compact('pegawai'));
    }

    // Menampilkan form tambah pegawai
    public function create()
    {
        $jabatan = Jabatan::all();
        return view('pegawai.create', compact('jabatan'));
    }

    // Menyimpan data pegawai baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai'   => 'required|string',
            'username'       => 'required|string|unique:pegawai,username',
            'email'          => 'required|email|unique:pegawai,email',
            'password'       => 'required|string|min:6',
            'notelp'         => 'required|string',
            'tanggal_lahir'  => 'required|date',
            'id_jabatan'     => 'required|exists:jabatan,id_jabatan'
        ]);

        $data = $request->all();
        $data['status_aktif'] = 1;

        // 🔐 Hash password sebelum disimpan
        $data['password'] = Hash::make($request->password);

        Pegawai::create($data);

        return redirect()->route('pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    // Menampilkan form edit pegawai
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $jabatan = Jabatan::all();
        return view('pegawai.edit', compact('pegawai', 'jabatan'));
    }

    // Menyimpan update data pegawai
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pegawai' => 'required|string',
            'email' => 'required|email|unique:pegawai,email,' . $id . ',id_pegawai',
            'notelp' => 'required|string',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan'
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->except(['_token', '_method']));

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    // Menghapus data pegawai
    public function destroy($id)
    {
        Pegawai::findOrFail($id)->delete();
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }

    public function nonaktifkan($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->status_aktif = 0;
        $pegawai->save();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dinonaktifkan.');
    }

    public function aktifkan($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->status_aktif = 1;
        $pegawai->save();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diaktifkan kembali.');
    }

    public function search(Request $request)
    {
        $query = Pegawai::with('jabatan');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orWhere('notelp', 'like', "%$search%")
                ->orWhereHas('jabatan', function ($q2) use ($search) {
                    $q2->where('nama_jabatan', 'like', "%$search%");
                });
            });
        }

        $pegawai = $query->get();
        return view('pegawai.index', compact('pegawai'));
    }

    public function showM(Request $request)
    {
        // Ambil pegawai yang sedang login
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            return response()->json(['error' => 'Pegawai tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'pegawai' => $pegawai
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