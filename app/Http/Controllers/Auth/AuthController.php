<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login manual (email + password).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses register manual.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'golongan_darah' => ['nullable', 'string', 'max:5'],
            'kontak_darurat' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'no_hp' => $validated['no_hp'] ?? null,
            'golongan_darah' => $validated['golongan_darah'] ?? null,
            'kontak_darurat' => $validated['kontak_darurat'] ?? null,
            'role' => 'Runner', // Default role untuk registrasi manual
        ]);

        Auth::login($user);

        return redirect()->route('runner.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di RunFest.');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Redirect user berdasarkan role setelah login.
     */
    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            'SuperAdmin' => redirect()->route('events.index'),
            'Organizer' => redirect()->route('organizer.events'),
            'Marshal' => redirect()->route('marshal.scanner'),
            default => redirect()->route('runner.dashboard'),
        };
    }
}
