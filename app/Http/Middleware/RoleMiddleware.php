<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Memvalidasi apakah user yang login memiliki role yang diizinkan.
     * Juga memblokir akun dengan status_akun = 'Diblokir'.
     *
     * Penggunaan di route: middleware('role:SuperAdmin,Organizer')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Daftar role yang diizinkan (dipisahkan koma)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // Blokir akun yang berstatus 'Diblokir' — paksa logout
        if (isset($user->status_akun) && $user->status_akun === 'Diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', '🚫 Akun Anda telah diblokir oleh Administrator. Hubungi support untuk informasi lebih lanjut.');
        }

        $userRole = $user->role;

        // SuperAdmin memiliki akses penuh ke seluruh rute
        if ($userRole !== 'SuperAdmin' && !in_array($userRole, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
