<?php

namespace App\Http\Controllers;

use App\Mail\LinkMail;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function sendLink(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $url = 'http://reusemart.shop/changePassword';

        // Kirim email
        Mail::to($data['email'])
            ->send(new LinkMail($url));

        return redirect()->route('login')->with('success', 'Link berhasil dikirim ke ' . $data['email']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'tipe_user' => 'required|in:pembeli,penitip,organisasi',
            'password' => 'required',
        ]);

        $tipe = $request->tipe_user;

        if($tipe === 'pembeli'){
            $request->validate(['email' => 'required|email']);

            $pembeli = Pembeli::where('email', $request->email)->first();

            $pembeli->update([
                'password' => $request->password,
            ]);

            return redirect()
            ->route('login')
            ->with('success', 'Berhasil Ubah Password.');
        }
        
        if($tipe === 'penitip'){
            $request->validate(['email' => 'required|email']);

            $penitip = Penitip::where('email', $request->email)->first();

            $penitip->update([
                'password' => $request->password,
            ]);

            return redirect()
            ->route('login')
            ->with('success', 'Berhasil Ubah Password.');
        }

        if($tipe === 'organisasi'){
            $request->validate(['email' => 'required|email']);

            $organisasi = Organisasi::where('email', $request->email)->first();

            $organisasi->update([
                'password' => $request->password,
            ]);

            return redirect()
            ->route('login')
            ->with('success', 'Berhasil Ubah Password.');
        }
        
        return back()->withErrors(['error' => 'Email atau password salah.']);
    }
}
