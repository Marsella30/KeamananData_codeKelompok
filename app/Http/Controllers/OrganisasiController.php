<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrganisasiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $organisasi = $q
            ? Organisasi::where('nama_organisasi', 'like', "%{$q}%")->get()
            : Organisasi::all();

        return view('Admin.organisasiIndex', [
            'organisasi' => $organisasi,
            'q'           => $q,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'alamat'          => 'required|string|max:255',
            'password'        => 'required|string|min:6',
            'email'           => 'required',
        ]);

        Organisasi::create([
            'nama_organisasi' => $request->nama_organisasi,
            'alamat'          => $request->alamat,
            'email'           => $request->email,
            'password'        => $request->password,
            'status_aktif'    => 1,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Organisasi berhasil dibuat.');
    }

    public function show(Request $request)
    {
        // Ambil query pencarian dari ?q=...
        $q = $request->query('q');

        // Bangun query
        $builder = Organisasi::query();
        if ($q) {
            $builder->where('nama_organisasi', 'like', "%{$q}%");
        }

        // Eksekusi dan kirim ke view
        $organisasi = $builder->get();

        return view('Admin.organisasiIndex', [
            'organisasi' => $organisasi,
            'q'          => $q,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'alamat'          => 'required|string|max:255',
        ]);

        $org = Organisasi::findOrFail($id);
        $org->update($request->only('nama_organisasi', 'alamat'));

        return redirect()
            ->route('organisasi.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Organisasi::destroy($id);

        return redirect()
            ->route('organisasi.index')
            ->with('success', 'Organisasi berhasil dihapus.');
    }

    public function nonaktif($id)
    {
        $org = Organisasi::findOrFail($id);
        $org->update(['status_aktif' => 0]);

        return redirect()
            ->route('organisasi.index')
            ->with('success', 'Organisasi berhasil dinonaktifkan.');
    }

    public function ubahPassword(Request $request, $id)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|confirmed',
        ]);

        $organisasi = Organisasi::findOrFail($id);

        // cek password lama
        if (! Hash::check($request->current_password, $organisasi->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama salah.'])
                ->withInput();
        }

        // simpan password baru
        $organisasi->password = Hash::make($request->new_password);
        $organisasi->save();

        return redirect()
            ->route('organisasi.index')
            ->with('success', 'Password berhasil diubah.');
    }
}