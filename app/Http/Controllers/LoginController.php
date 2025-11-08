<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $email = session('pending_email');
        $role = session('pending_role');

        if (!$email || !$role) {
            return redirect()->route('login')->withErrors(['error' => 'Sesi login tidak valid.']);
        }

        $user = \App\Models\Pembeli::where('email', $email)->first();

        if (!$user || $user->otp_code !== $request->otp || $user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kadaluarsa.']);
        }

        // OTP valid → hapus dan login
        $user->update(['otp_code' => null, 'otp_expires_at' => null]);

        Auth::guard('pembeli')->login($user);
        $request->session()->regenerate();

        // hapus sesi sementara
        session()->forget(['pending_email', 'pending_role']);

        return redirect()->route('home')->with('success', 'Login berhasil!');
    }

    public function showLoginForm()
    {
        $organisasiList = \App\Models\Organisasi::where('status_aktif', 1)->get();
        return view('login', compact('organisasiList'));
    }

    public function login(Request $request)
    {
        // 1. Validasi input umum
        $request->validate([
            'tipe_user' => 'required|in:pembeli,penitip,organisasi,pegawai',
            'password'  => 'required|string',
        ]);

        $tipe = $request->tipe_user;
        $password = $request->password;

        switch ($tipe) {

            // =================== PEMBELI ===================
            case 'pembeli':
                $request->validate(['email' => 'required|email']);
                $user = \App\Models\Pembeli::where('email', $request->email)->first();

                if (!$user) {
                    $existsInOther = \App\Models\Penitip::where('email', $request->email)->exists()
                                || \App\Models\Organisasi::where('email', $request->email)->exists()
                                || \App\Models\Pegawai::where('email', $request->email)->exists();

                    $msg = $existsInOther 
                        ? 'Email terdaftar tapi bukan sebagai Pembeli.' 
                        : 'Email tidak terdaftar.';
                    return back()->withErrors(['error' => $msg]);
                }

                if (!Hash::check($password, $user->password)) {
                    return back()->withErrors(['error' => 'Password salah untuk Pembeli.']);
                }

                // Generate OTP
                $otp = rand(100000, 999999);
                try {
                    \DB::table('pembeli')
                        ->where('id_pembeli', $user->id_pembeli)
                        ->update([
                            'otp_code' => $otp,
                            'otp_expires_at' => now()->addMinutes(5),
                        ]);
                    Log::info('✅ OTP tersimpan', ['email' => $user->email, 'otp' => $otp]);
                } catch (\Throwable $e) {
                    Log::error('❌ Gagal menyimpan OTP: '.$e->getMessage());
                    return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan OTP.']);
                }

                try {
                    Mail::to($user->email)->send(new OtpMail($otp));
                } catch (\Throwable $e) {
                    Log::error('⚠️ Gagal kirim OTP: '.$e->getMessage());
                }

                session([
                    'pending_email' => $user->email,
                    'pending_role'  => 'pembeli',
                ]);

                return redirect()->route('otp.show')
                    ->with('info', 'Kode OTP telah dikirim ke email Anda dan berlaku 5 menit.');

            // =================== PENITIP ===================
            case 'penitip':
                $request->validate(['email' => 'required|email']);
                $user = \App\Models\Penitip::where('email', $request->email)->first();

                if (!$user) {
                    $existsInOther = \App\Models\Pembeli::where('email', $request->email)->exists()
                                || \App\Models\Organisasi::where('email', $request->email)->exists()
                                || \App\Models\Pegawai::where('email', $request->email)->exists();

                    $msg = $existsInOther 
                        ? 'Email terdaftar tapi bukan sebagai Penitip.' 
                        : 'Email tidak terdaftar.';
                    return back()->withErrors(['error' => $msg]);
                }

                if (!Hash::check($password, $user->password)) {
                    return back()->withErrors(['error' => 'Password salah untuk Penitip.']);
                }

                Auth::guard('penitip')->login($user);
                $request->session()->regenerate();
                Log::info('✅ Penitip login', ['email' => $user->email]);
                return redirect()->route('dashboard.penitip');

            // =================== ORGANISASI ===================
            case 'organisasi':
                $request->validate(['id_organisasi' => 'required|exists:organisasi,id_organisasi']);
                $org = \App\Models\Organisasi::find($request->id_organisasi);

                if (!$org) {
                    return back()->withErrors(['error' => 'Organisasi tidak ditemukan.']);
                }

                if (!Hash::check($password, $org->password)) {
                    return back()->withErrors(['error' => 'Password salah untuk Organisasi.']);
                }

                Auth::guard('organisasi')->login($org);
                $request->session()->regenerate();
                Log::info('✅ Organisasi login', ['id_organisasi' => $org->id_organisasi]);
                return redirect()->route('organisasi.request.index');

            // =================== PEGawai ===================
            case 'pegawai':
                $request->validate(['email' => 'required|email']);
                $pegawai = \App\Models\Pegawai::where('email', $request->email)->first();

                if (!$pegawai) {
                    $existsInOther = \App\Models\Pembeli::where('email', $request->email)->exists()
                                || \App\Models\Penitip::where('email', $request->email)->exists()
                                || \App\Models\Organisasi::where('email', $request->email)->exists();

                    $msg = $existsInOther 
                        ? 'Email terdaftar tapi bukan sebagai Pegawai.' 
                        : 'Email tidak terdaftar.';
                    return back()->withErrors(['error' => $msg]);
                }

                if (!Hash::check($password, $pegawai->password)) {
                    return back()->withErrors(['error' => 'Password salah untuk Pegawai.']);
                }

                Auth::guard('pegawai')->login($pegawai);
                $request->session()->regenerate();

                $jabatan = strtolower(trim($pegawai->jabatan->nama_jabatan));
                $dashboardRoutes = [
                    'admin' => 'dashboard.admin',
                    'kurir' => 'dashboard.kurir',
                    'owner' => 'dashboard.owner',
                    'kepala gudang' => 'dashboard.kepala_gudang',
                    'pegawai gudang' => 'dashboard.pegawai_gudang',
                    'customer service' => 'dashboard.cs',
                ];

                Log::info('✅ Pegawai login', ['email' => $pegawai->email, 'jabatan' => $jabatan]);

                return redirect()->route($dashboardRoutes[$jabatan] ?? 'dashboard.pegawai');

            default:
                return back()->withErrors(['error' => 'Login gagal. Periksa kembali data Anda.']);
        }
    }

    // public function login(Request $request)
    // {
    //     \DB::listen(function ($query) {
    //         \Log::info('SQL', [$query->sql, $query->bindings]);
    //     });

    //     $request->validate([
    //         'tipe_user' => 'required|in:pembeli,penitip,organisasi,pegawai',
    //         'password' => 'required|string',
    //     ]);

    //     $tipe = $request->tipe_user;
    //     $password = $request->password;

    //     if ($tipe === 'pembeli') {
    //         // 1. Validasi input
    //         $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ]);

    //         // 2. Cek apakah email terdaftar
    //         $user = \App\Models\Pembeli::where('email', $request->email)->first();
    //         if (!$user) {
    //             return back()->withErrors(['error' => 'Email tidak terdaftar.']);
    //         }

    //         // 3. Cek password hash
    //         if (!Hash::check($password, $user->password)) {
    //             return back()->withErrors(['error' => 'Password salah.']);
    //         }

    //         // 4. Generate OTP 6 digit
    //         $otp = rand(100000, 999999);

    //         // 5. Simpan OTP ke database (langsung lewat DB agar pasti tersimpan)
    //         try {
    //             \DB::table('pembeli')
    //                 ->where('id_pembeli', $user->id_pembeli)
    //                 ->update([
    //                     'otp_code' => $otp,
    //                     'otp_expires_at' => now()->addMinutes(5),
    //                 ]);

    //             Log::info('✅ OTP tersimpan ke DB', [
    //                 'email' => $user->email,
    //                 'otp' => $otp,
    //             ]);
    //         } catch (\Throwable $e) {
    //             Log::error('❌ Gagal menyimpan OTP: '.$e->getMessage());
    //             return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan OTP.']);
    //         }

    //         // 6. Kirim OTP ke email (jika gagal, tidak rollback)
    //         try {
    //             Mail::to($user->email)->send(new OtpMail($otp));
    //         } catch (\Throwable $e) {
    //             Log::error('⚠️ Gagal mengirim email OTP: '.$e->getMessage());
    //             // Tetap lanjut agar user bisa input OTP manual
    //         }

    //         // 7. Simpan data sementara untuk verifikasi OTP nanti
    //         session([
    //             'pending_email' => $user->email,
    //             'pending_role' => 'pembeli',
    //         ]);

    //         // 8. Arahkan ke halaman verifikasi OTP
    //         return redirect()
    //             ->route('otp.show')
    //             ->with('info', 'Kode OTP telah dikirim ke email Anda dan berlaku selama 5 menit.');
    //     }

    //     if ($tipe === 'penitip') {
    //         $request->validate(['email' => 'required|email']);
    //         $user = \App\Models\Penitip::where('email', $request->email)->first();

    //         if ($user && Hash::check($password, $user->password)) {
    //             Auth::guard('penitip')->login($user);
    //             $request->session()->regenerate();
    //             return redirect()->route('dashboard.penitip');
    //         }

    //         return back()->withErrors(['error' => 'Email atau password salah.']);
    //     }

    //     if ($tipe === 'organisasi') {
    //         $request->validate(['id_organisasi' => 'required|exists:organisasi,id_organisasi']);
    //         $org = \App\Models\Organisasi::find($request->id_organisasi);
    //         if ($org && $org->password === $password) {
    //             Auth::guard('organisasi')->login($org);
    //             $request->session()->regenerate();
    //             return redirect()->route('organisasi.request.index');
    //             // return redirect()->route('dashboard.organisasi');
    //         }
    //     }

    //     if ($tipe === 'pegawai') {
    //         $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required|string',
    //         ]);
        
    //         $pegawai = \App\Models\Pegawai::where('email', $request->email)->first();
        
    //         if ($pegawai && $pegawai->password === $request->password) {
    //             Auth::guard('pegawai')->login($pegawai);
    //             $request->session()->regenerate();
        
    //             $jabatan = strtolower(trim($pegawai->jabatan->nama_jabatan));
        
    //             switch ($jabatan) {
    //                 case 'admin':
    //                     return redirect()->route('dashboard.admin');
    //                 case 'kurir':
    //                     return redirect()->route('dashboard.kurir');
    //                 case 'owner':
    //                     return redirect()->route('dashboard.owner');
    //                     // return redirect()->route('dashboard.pembeli');
    //                 case 'kepala gudang':
    //                     return redirect()->route('dashboard.kepala_gudang');
    //                 case 'pegawai gudang':
    //                     return redirect()->route('dashboard.pegawai_gudang');
    //                 case 'customer service':
    //                     return redirect()->route('dashboard.cs');
    //                 default:
    //                     return redirect()->route('dashboard.pegawai');
    //             }
    //         }
        
    //         return back()->withErrors(['error' => 'Email atau password salah.']);
    //     }
        

    //     return back()->withErrors(['error' => 'Login gagal. Periksa kembali data Anda.']);
    // }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function loginMobile(Request $request)
    {
        // Validasi input, semua tipe user lowercase
        $request->validate([
            'tipe_user' => 'required|in:pembeli,penitip,kurir,hunter',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $tipe = strtolower($request->tipe_user);
        $email = $request->email;
        $password = trim($request->password);

        Log::info('Login attempt', ['tipe_user' => $tipe, 'email' => $email]);

        if ($tipe === 'pembeli') {
            $user = Pembeli::where('email', $email)->first();

            if (!$user) {
                Log::info('User pembeli tidak ditemukan', ['email' => $email]);
            } elseif ($user->status_aktif != 1) {
                Log::info('User pembeli tidak aktif', ['email' => $email, 'status_aktif' => $user->status_aktif]);
            } elseif ($password === $user->password) {
                $token = $user->createToken('ReUseMart')->plainTextToken;
                Log::info('Login pembeli sukses', ['email' => $email]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $user,
                    'redirect_page' => 'dashboard.pembeli',
                ]);
            } else {
                Log::info('Password salah pembeli', ['email' => $email]);
            }
        }

        if ($tipe === 'penitip') {
            $user = Penitip::where('email', $email)->first();

            if (!$user) {
                Log::info('User penitip tidak ditemukan', ['email' => $email]);
            } elseif ($user->status_aktif != 1) {
                Log::info('User penitip tidak aktif', ['email' => $email, 'status_aktif' => $user->status_aktif]);
            } elseif ($password === $user->password) {
                $token = $user->createToken('ReUseMart')->plainTextToken;
                Log::info('Login penitip sukses', ['email' => $email]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $user,
                    'redirect_page' => 'dashboard.penitip',
                ]);
            } else {
                Log::info('Password salah penitip', ['email' => $email]);
            }
        }

        if ($tipe === 'kurir' || $tipe === 'hunter') {
            $user = Pegawai::where('email', $email)
                ->whereIn('id_jabatan', [5, 7])
                ->first();

            if (!$user) {
                Log::info('User pegawai tidak ditemukan', ['email' => $email]);
            } elseif ($password === $user->password) {
                $token = $user->createToken('ReUseMart')->plainTextToken;
                $redirectPage = $user->id_jabatan == 5 ? 'dashboard.kurir' : 'dashboard.hunter';
                Log::info('Login pegawai sukses', ['email' => $email, 'id_jabatan' => $user->id_jabatan]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $user,
                    'redirect_page' => $redirectPage,
                ]);
            } else {
                Log::info('Password salah pegawai', ['email' => $email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pegawai dengan id_jabatan 5 atau 7 yang bisa login.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'error' => 'Email atau password salah.',
        ]);
    }

    public function logoutMobile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            // Menghapus token yang sedang digunakan
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan atau sudah logout.',
        ]);
    }
}
