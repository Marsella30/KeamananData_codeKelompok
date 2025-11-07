<?php

namespace App\Http\Controllers;

use App\Models\AlamatPembeli;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AlamatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $alamat = AlamatPembeli::where('id_pembeli', $user->id_pembeli)->get();

        $pembeli = $user;

        // Return the view with alamat data
        return view('Pembeli.alamatPembeli', compact('alamat', 'pembeli'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'jalan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255',
        ]);

        $alamat = AlamatPembeli::create([
            'id_pembeli' => $user->id_pembeli,
            'jalan' => $request->jalan,
            'kelurahan' => $request->kelurahan,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kode_pos' => $request->kode_pos,
            'detail' => $request->detail,
        ]);

        return redirect()->route('alamatPembeli.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $alamat = AlamatPembeli::findOrFail($id);

        return view('editAlamat', compact('alamat'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'jalan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255',
        ]);

        $alamat = AlamatPembeli::findOrFail($id);

        $alamat->update([
            'jalan' => $request->jalan,
            'kelurahan' => $request->kelurahan,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kode_pos' => $request->kode_pos,
            'detail' => $request->detail,
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $alamat = AlamatPembeli::where('id_alamat_pembeli', $id)
            ->where('id_pembeli', $user->id_pembeli)
            ->first();

        if ($alamat) {
            $jumlahAlamat = AlamatPembeli::where('id_pembeli', $user->id_pembeli)->count();

            // Prevent deletion if there is only 1 address
            if ($jumlahAlamat == 1) {
                // Redirect dengan flash message untuk alert
                return redirect()->route('alamatPembeli.index')->with('error', 'Alamat default tidak dapat dihapus jika hanya ada satu alamat.');
            }

            $alamat->delete();

            // Redirect dengan flash message untuk alert
            return redirect()->route('alamatPembeli.index')->with('success', 'Alamat berhasil dihapus.');
        }

        // Jika alamat tidak ditemukan, redirect dengan alert
        return redirect()->route('alamatPembeli.index')->with('error', 'Alamat tidak ditemukan.');
    }

}
