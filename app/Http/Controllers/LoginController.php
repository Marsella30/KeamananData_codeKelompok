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
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{

    public function sendTooManyAttemptsResponse($request)
    {
        $retryAfter = RateLimiter::availableIn('login', $request->ip());

        return response()->json([
            'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
            'retry_after' => $retryAfter
        ], 429);
    }

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

        // Log percobaan login
        Log::info('Login attempt (web)', [
            'tipe_user' => $tipe,
            'email' => $request->email ?? 'N/A',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        switch ($tipe) {

            // =================== PEMBELI ===================
            case 'pembeli':
                // Validasi email wajib ada
                $request->validate(['email' => 'required|email']);

                // Ambil data user Pembeli
                $user = \App\Models\Pembeli::where('email', $request->email)->first();

                if (!$user) {
                    // Cek email di tabel lain
                    $existsPenitip = Penitip::where('email', $request->email)->exists();
                    $existsOrganisasi = Organisasi::where('email', $request->email)->exists();
                    $existsPegawai = Pegawai::where('email', $request->email)->exists();

                    // Logging debug
                    Log::info('Cek email di tabel lain', [
                        'penitip' => $existsPenitip,
                        'organisasi' => $existsOrganisasi,
                        'pegawai' => $existsPegawai,
                    ]);

                    // Tentukan pesan error
                    $msg = ($existsPenitip || $existsOrganisasi || $existsPegawai)
                        ? 'Email terdaftar tapi bukan sebagai Pembeli.'
                        : 'Email tidak terdaftar.';

                    // Logging gagal login
                    Log::warning('Login gagal (web) - pembeli', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);

                    return response()->json(['error' => $msg], 422);
                }

                if (!Hash::check($password, $user->password)) {
                    $msg = 'Password salah untuk Pembeli.';
                    Log::warning('Login gagal (web) - pembeli', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                // Generate OTP 6 digit
                $otp = rand(100000, 999999);

                // Simpan OTP & kadaluarsa
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
                    return response()->json(['error' => 'Terjadi kesalahan saat menyimpan OTP.'], 500);
                }

                // Kirim OTP via email
                try {
                    Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
                } catch (\Throwable $e) {
                    Log::error('⚠️ Gagal kirim OTP: '.$e->getMessage());
                }

                // Simpan session sementara untuk login OTP
                session([
                    'pending_email' => $user->email,
                    'pending_role'  => 'pembeli',
                ]);

                Log::info('Login sukses (web) - pembeli', ['email' => $user->email]);

                return response()->json([
                    'success' => true,
                    'redirect_page' => route('otp.show'),
                    'info' => 'Kode OTP telah dikirim ke email Anda dan berlaku 5 menit.'
                ]);

            // =================== PENITIP ===================
            case 'penitip':
                $request->validate(['email' => 'required|email']);
                $user = Penitip::where('email', $request->email)->first();

                if (!$user) {
                    $msg = Pembeli::where('email', $request->email)->exists() ||
                           Organisasi::where('email', $request->email)->exists() ||
                           Pegawai::where('email', $request->email)->exists()
                           ? 'Email terdaftar tapi bukan sebagai Penitip.'
                           : 'Email tidak terdaftar.';

                    Log::warning('Login gagal (web) - penitip', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                if (!Hash::check($password, $user->password)) {
                    $msg = 'Password salah untuk Penitip.';
                    Log::warning('Login gagal (web) - penitip', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                Auth::guard('penitip')->login($user);
                $request->session()->regenerate();
                Log::info('Login sukses (web) - penitip', ['email' => $user->email]);

                return response()->json([
                    'success' => true,
                    'redirect_page' => route('dashboard.penitip')
                ]);

            // =================== ORGANISASI ===================
            case 'organisasi':
                $request->validate(['id_organisasi' => 'required|exists:organisasi,id_organisasi']);
                $org = Organisasi::find($request->id_organisasi);

                if (!$org) {
                    $msg = 'Organisasi tidak ditemukan.';
                    Log::warning('Login gagal (web) - organisasi', [
                        'id_organisasi' => $request->id_organisasi,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                if (!Hash::check($password, $org->password)) {
                    $msg = 'Password salah untuk Organisasi.';
                    Log::warning('Login gagal (web) - organisasi', [
                        'id_organisasi' => $request->id_organisasi,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                Auth::guard('organisasi')->login($org);
                $request->session()->regenerate();
                Log::info('Login sukses (web) - organisasi', ['id_organisasi' => $org->id_organisasi]);

                return response()->json([
                    'success' => true,
                    'redirect_page' => route('organisasi.request.index')
                ]);

            // =================== PEGawai ===================
            case 'pegawai':
                $request->validate(['email' => 'required|email']);
                $pegawai = Pegawai::where('email', $request->email)->first();

                if (!$pegawai) {
                    $msg = Pembeli::where('email', $request->email)->exists() ||
                           Penitip::where('email', $request->email)->exists() ||
                           Organisasi::where('email', $request->email)->exists()
                           ? 'Email terdaftar tapi bukan sebagai Pegawai.'
                           : 'Email tidak terdaftar.';

                    Log::warning('Login gagal (web) - pegawai', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
                }

                if (!Hash::check($password, $pegawai->password)) {
                    $msg = 'Password salah untuk Pegawai.';
                    Log::warning('Login gagal (web) - pegawai', [
                        'email' => $request->email,
                        'reason' => $msg,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    return response()->json(['error' => $msg], 422);
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

                Log::info('Login sukses (web) - pegawai', ['email' => $pegawai->email, 'jabatan' => $jabatan]);

                return response()->json([
                    'success' => true,
                    'redirect_page' => route($dashboardRoutes[$jabatan] ?? 'dashboard.pegawai')
                ]);

            default:
                $msg = 'Login gagal. Periksa kembali data Anda.';
                Log::warning('Login gagal (web) - unknown type', [
                    'tipe_user' => $tipe,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                return response()->json(['error' => $msg], 422);
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
        $request->validate([
            'tipe_user' => 'required|in:pembeli,penitip,kurir,hunter',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $tipe = strtolower($request->tipe_user);
        $email = $request->email;
        $password = trim($request->password);

        // Log percobaan login API
        Log::info('Login attempt (API)', [
            'tipe_user' => $tipe,
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        switch ($tipe) {
            case 'pembeli':
                $user = Pembeli::where('email', $email)->first();
                if (!$user || !Hash::check($password, $user->password)) {
                    Log::warning('Login gagal (API) - pembeli', ['email' => $email, 'reason' => 'Email/password salah', 'ip' => $request->ip()]);
                    throw ValidationException::withMessages(['error' => 'Email atau password salah.']);
                }
                if ($user->status_aktif != 1) {
                    Log::warning('Login gagal (API) - pembeli', ['email' => $email, 'reason' => 'Akun tidak aktif', 'ip' => $request->ip()]);
                    throw ValidationException::withMessages(['error' => 'Akun tidak aktif.']);
                }
                $token = $user->createToken('ReUseMart')->plainTextToken;
                Log::info('Login sukses (API) - pembeli', ['email' => $email, 'ip' => $request->ip()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $user,
                    'redirect_page' => 'dashboard.pembeli',
                ]);

            case 'penitip':
                $user = Penitip::where('email', $email)->first();
                if (!$user || !Hash::check($password, $user->password)) {
                    Log::warning('Login gagal (API) - penitip', ['email' => $email, 'reason' => 'Email/password salah', 'ip' => $request->ip()]);
                    throw ValidationException::withMessages(['error' => 'Email atau password salah.']);
                }
                if ($user->status_aktif != 1) {
                    Log::warning('Login gagal (API) - penitip', ['email' => $email, 'reason' => 'Akun tidak aktif', 'ip' => $request->ip()]);
                    throw ValidationException::withMessages(['error' => 'Akun tidak aktif.']);
                }
                $token = $user->createToken('ReUseMart')->plainTextToken;
                Log::info('Login sukses (API) - penitip', ['email' => $email, 'ip' => $request->ip()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $user,
                    'redirect_page' => 'dashboard.penitip',
                ]);

            case 'kurir':
            case 'hunter':
                $pegawai = Pegawai::where('email', $email)
                    ->whereIn('id_jabatan', [5, 7])
                    ->first();

                if (!$pegawai || !Hash::check($password, $pegawai->password)) {
                    Log::warning('Login gagal (API) - pegawai', ['email' => $email, 'reason' => 'Email/password salah', 'ip' => $request->ip()]);
                    throw ValidationException::withMessages(['error' => 'Email atau password salah.']);
                }

                $token = $pegawai->createToken('ReUseMart')->plainTextToken;
                $redirectPage = $pegawai->id_jabatan == 5 ? 'dashboard.kurir' : 'dashboard.hunter';
                Log::info('Login sukses (API) - pegawai', ['email' => $email, 'jabatan' => $pegawai->id_jabatan, 'ip' => $request->ip()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user' => $pegawai,
                    'redirect_page' => $redirectPage,
                ]);

            default:
                Log::warning('Login gagal (API) - tipe user invalid', ['tipe_user' => $tipe, 'ip' => $request->ip()]);
                throw ValidationException::withMessages(['error' => 'Tipe user tidak valid.']);
        }
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
