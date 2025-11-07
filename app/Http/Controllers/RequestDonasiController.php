<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestDonasiController extends Controller
{
    public function index(Request $request)
{
    $organisasi_id = Auth::guard('organisasi')->user()->id_organisasi;
    $search = $request->input('q'); 

    $requestDonasis = RequestDonasi::where('id_organisasi', $organisasi_id)
        ->when($search, function ($query, $search) {
            return $query->where('barang_dibutuhkan', 'like', "%$search%")
                         ->orWhere('status_request', 'like', "%$search%");
        })
        ->paginate(10); 

    return view('dashboardO', [
        'requestDonasis' => $requestDonasis,
        'search' => $search
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'barang_dibutuhkan' => 'required|string',
        ]);

        RequestDonasi::create([
            'id_organisasi' => Auth::guard('organisasi')->user()->id_organisasi,
            'barang_dibutuhkan' => $request->barang_dibutuhkan,
            'status_request' => 'Diproses',
        ]);

        return redirect()->route('organisasi.request.index')->with('success', 'Request Donasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $requestDonasi = RequestDonasi::findOrFail($id);

        $request->validate([
            'barang_dibutuhkan' => 'required|string',
        ]);

        $requestDonasi->update([
            'barang_dibutuhkan' => $request->barang_dibutuhkan,
        ]);

        return redirect()->route('organisasi.request.index')->with('success', 'Request Donasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $requestDonasi = RequestDonasi::findOrFail($id);
        $requestDonasi->delete();

        return redirect()->route('organisasi.request.index')->with('success', 'Request Donasi berhasil dihapus.');
    }
}
