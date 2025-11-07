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


class LoginController extends Controller
{
    public function showLoginForm()
    {
        $organisasiList = \App\Models\Organisasi::where('status_aktif', 1)->get();
        return view('login', compact('organisasiList'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'tipe_user' => 'required|in:pembeli,penitip,organisasi,pegawai',
            'password' => 'required|string',
        ]);

        $tipe = $request->tipe_user;
        $password = $request->password;

        if ($tipe === 'pembeli') {
            $request->validate(['email' => 'required|email']);
            $user = \App\Models\Pembeli::where('email', $request->email)->first();
            if ($user && $user->password === $password) {
                Auth::guard('pembeli')->login($user);
                $request->session()->regenerate();
                return redirect()->route('home'); 
            }
        }

        if ($tipe === 'penitip') {
            $request->validate(['email' => 'required|email']);
            $user = \App\Models\Penitip::where('email', $request->email)->first();
            if ($user && $user->password === $password) {
                Auth::guard('penitip')->login($user);
                $request->session()->regenerate();
                return redirect()->route('dashboard.penitip');
                // return redirect()->route('home');
            }
        }

        if ($tipe === 'organisasi') {
            $request->validate(['id_organisasi' => 'required|exists:organisasi,id_organisasi']);
            $org = \App\Models\Organisasi::find($request->id_organisasi);
            if ($org && $org->password === $password) {
                Auth::guard('organisasi')->login($org);
                $request->session()->regenerate();
                return redirect()->route('organisasi.request.index');
                // return redirect()->route('dashboard.organisasi');
            }
        }

        if ($tipe === 'pegawai') {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        
            $pegawai = \App\Models\Pegawai::where('email', $request->email)->first();
        
            if ($pegawai && $pegawai->password === $request->password) {
                Auth::guard('pegawai')->login($pegawai);
                $request->session()->regenerate();
        
                $jabatan = strtolower(trim($pegawai->jabatan->nama_jabatan));
        
                switch ($jabatan) {
                    case 'admin':
                        return redirect()->route('dashboard.admin');
                    case 'kurir':
                        return redirect()->route('dashboard.kurir');
                    case 'owner':
                        return redirect()->route('dashboard.owner');
                        // return redirect()->route('dashboard.pembeli');
                    case 'kepala gudang':
                        return redirect()->route('dashboard.kepala_gudang');
                    case 'pegawai gudang':
                        return redirect()->route('dashboard.pegawai_gudang');
                    case 'customer service':
                        return redirect()->route('dashboard.cs');
                    default:
                        return redirect()->route('dashboard.pegawai');
                }
            }
        
            return back()->withErrors(['error' => 'Email atau password salah.']);
        }
        

        return back()->withErrors(['error' => 'Login gagal. Periksa kembali data Anda.']);
    }


////////////////////////////////// login hash ///////////////////////////////////

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'tipe_user' => 'required|in:pembeli,penitip,organisasi,pegawai',
    //         'password' => 'required|string',
    //     ]);

    //     $tipe = $request->tipe_user;
    //     $password = $request->password;

    //     if ($tipe === 'pembeli') {
    //         $request->validate(['email' => 'required|email']);
    //         $user = \App\Models\Pembeli::where('email', $request->email)->first();
    //         if ($user && Hash::check($password, $user->password)) {
    //             Auth::guard('pembeli')->login($user);
    //             $request->session()->regenerate();
    //             return redirect()->route('home');
    //         }
    //     }

    //     if ($tipe === 'penitip') {
    //         $request->validate(['email' => 'required|email']);
    //         $user = \App\Models\Penitip::where('email', $request->email)->first();
    //         if ($user && Hash::check($password, $user->password)) {
    //             Auth::guard('penitip')->login($user);
    //             $request->session()->regenerate();
    //             return redirect()->route('home');
    //         }
    //     }

    //     if ($tipe === 'organisasi') {
    //         $request->validate(['id_organisasi' => 'required|exists:organisasi,id_organisasi']);
    //         $org = \App\Models\Organisasi::find($request->id_organisasi);
    //         if ($org && Hash::check($password, $org->password)) {
    //             Auth::guard('organisasi')->login($org);
    //             $request->session()->regenerate();
    //             return redirect()->route('organisasi.request.index');
    //         }
    //     }

    //     if ($tipe === 'pegawai') {
    //         $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required|string',
    //         ]);

    //         $pegawai = \App\Models\Pegawai::where('email', $request->email)->first();

    //         if ($pegawai && Hash::check($password, $pegawai->password)) {
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

/////////////////////////////////////////////////// MOBILE //////////////////////////////////////////////////////

    // public function loginMobile(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'tipe_user' => 'required|in:pembeli,penitip,kurir,hunter',
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     $tipe = strtolower($request->tipe_user);
    //     $email = $request->email;
    //     $password = $request->password;

    //     if ($tipe === 'pembeli') {
    //         $user = Pembeli::where('email', $email)->first();
    //         if ($user && Hash::check($password, $user->password)) {
    //             $token = $user->createToken('ReUseMart')->plainTextToken;
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Login berhasil!',
    //                 'token' => $token,
    //                 'user' => $user,
    //                 'redirect_page' => 'dashboard.pembeli',  // Redirect khusus pembeli
    //             ]);
    //         }
    //     }

    //     if ($tipe === 'penitip') {
    //         $user = Penitip::where('email', $email)->first();
    //         if ($user && Hash::check($password, $user->password)) {
    //             $token = $user->createToken('ReUseMart')->plainTextToken;
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Login berhasil!',
    //                 'token' => $token,
    //                 'user' => $user,
    //                 'redirect_page' => 'dashboard.penitip',  // Redirect khusus penitip
    //             ]);
    //         }
    //     }

    //     if ($tipe === 'kurir' || $tipe === 'hunter') {
    //         $user = Pegawai::where('email', $email)
    //             ->whereIn('id_jabatan', [5, 7])
    //             ->first();

    //         if ($user && Hash::check($password, $user->password)) {
    //             $token = $user->createToken('ReUseMart')->plainTextToken;
    //             $redirectPage = '';
    //             if ($user->id_jabatan == 5) {
    //                 $redirectPage = 'dashboard.kurir';
    //             } elseif ($user->id_jabatan == 7) {
    //                 $redirectPage = 'dashboard.hunter';
    //             }
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Login berhasil!',
    //                 'token' => $token,
    //                 'user' => $user,
    //                 'redirect_page' => $redirectPage,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Hanya pegawai dengan id_jabatan 5 atau 7 yang bisa login.',
    //             ]);
    //         }
    //     }

    //     throw ValidationException::withMessages([
    //         'error' => 'Email atau password salah.',
    //     ]);
    // }

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
