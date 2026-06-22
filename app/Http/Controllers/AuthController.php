<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Menampilkan halaman form login
    public function showLoginForm()
    {
        // Jika sudah login, langsung usir ke jalurnya masing-masing
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    // 2. Memproses data yang diketik user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Proteksi karyawan resign (opsional, karena API sudah dijaga, web juga harus)
            if ($user->is_resign == 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini telah dinonaktifkan (Resign).']);
            }

            $request->session()->regenerate();
            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // 3. Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // 4. LOGIKA PERCABANGAN (ROLE CHECKER)
    private function redirectUser()
    {
        $user = Auth::user();
        
        // ⚠️ PENTING: Ganti logika ini sesuai caramu menandai Admin di tabel users
        // Misalnya memakai kolom 'role' == 'admin' atau mengecek email spesifik HRD
        if ($user->email === 'admin@esa.com' || $user->email === 'hrd@esa.com') { 
            return redirect()->intended('/admin'); // Terbangkan ke Filament
        }

        // Jika bukan admin, terbangkan ke halaman web karyawan
        return redirect()->intended('/karyawan/dashboard');
    }
}