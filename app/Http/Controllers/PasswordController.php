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
            'password' => 'required|min:6',
            'email'    => 'required|email',
        ]);

        $tipe = $request->tipe_user;

        switch($tipe){
            case 'pembeli':
                $user = Pembeli::where('email', $request->email)->first();
                break;

            case 'penitip':
                $user = Penitip::where('email', $request->email)->first();
                break;

            case 'organisasi':
                $user = Organisasi::where('email', $request->email)->first();
                break;

            default:
                return back()->withErrors(['error' => 'Tipe user tidak valid.']);
        }

        if (!$user) {
            return back()->withErrors(['error' => 'Email tidak ditemukan.']);
        }

        // 🔐 Hash password sebelum disimpan
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Berhasil ubah password.');
    }
}
