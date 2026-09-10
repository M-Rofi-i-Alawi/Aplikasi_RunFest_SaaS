<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user ke halaman Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth.
     * Jika user sudah ada (berdasarkan email), update google_id-nya.
     * Jika user baru, buat akun baru dengan role Runner.
     */
    public function handleGoogleCallback(\Illuminate\Http\Request $request)
    {
        // ============================================================
        // Deteksi: User membatalkan login Google
        // Google mengirim ?error=access_denied atau tanpa ?code
        // ============================================================
        if ($request->has('error') || !$request->has('code')) {
            return redirect()->route('login')
                ->with('info', 'Login dengan Google dibatalkan.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email terlebih dahulu (support merge akun manual + Google)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User sudah ada, update google_id jika belum ada
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                // Buat user baru dari Google OAuth
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'nama'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'password'  => null, // Nullable, karena login via Google
                    'role'      => 'Runner',
                ]);
            }

            Auth::login($user, true);

            return match ($user->role) {
                'SuperAdmin' => redirect()->route('events.index'),
                'Organizer'  => redirect()->route('organizer.events'),
                'Marshal'    => redirect()->route('marshal.scanner'),
                default      => redirect()->route('runner.dashboard'),
            };

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
