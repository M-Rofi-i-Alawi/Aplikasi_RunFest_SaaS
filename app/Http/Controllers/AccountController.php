<?php

namespace App\Http\Controllers;

use App\Models\RekeningOrganizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan Akun & Profil.
     */
    public function index()
    {
        $user = auth()->user();
        $rekening = collect();

        // Load rekening hanya untuk Organizer & SuperAdmin
        if ($user->isOrganizer() || $user->isSuperAdmin()) {
            $rekening = $user->rekening()->orderByDesc('is_primary')->get();
        }

        return view('account.settings', compact('user', 'rekening'));
    }

    /**
     * Update Profil: Nama, No HP, Golongan Darah, Kontak Darurat, Jersey, NIK, Foto Identitas.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'golongan_darah' => 'nullable|string|max:5',
            'kontak_darurat' => 'nullable|string|max:255',
            'ukuran_jersey_default' => ['nullable', Rule::in(['S', 'M', 'L', 'XL', 'XXL'])],
            'nik' => ['nullable', 'numeric', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id_user, 'id_user')],
            'foto_identitas' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nik.digits' => 'NIK/NISN harus berjumlah tepat 16 digit angka.',
            'nik.numeric' => 'NIK/NISN hanya boleh berisi angka.',
            'nik.unique' => 'NIK/NISN ini sudah terdaftar oleh pengguna lain.',
            'foto_identitas.max' => 'Ukuran foto identitas maksimal 2MB.',
            'foto_identitas.mimes' => 'Format foto identitas harus berupa JPG, JPEG, PNG, atau WEBP.',
        ]);

        // Handle upload foto identitas
        if ($request->hasFile('foto_identitas')) {
            // Hapus file lama jika ada
            if ($user->foto_identitas && Storage::disk('public')->exists($user->foto_identitas)) {
                Storage::disk('public')->delete($user->foto_identitas);
            }

            // Simpan file baru ke storage/app/public/identitas/
            $path = $request->file('foto_identitas')->store('identitas', 'public');
            $validated['foto_identitas'] = $path;
        }

        $user->update($validated);

        return redirect()->route('account.settings')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update Password (hanya untuk pengguna manual, bukan OAuth).
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Blokir perubahan password untuk user OAuth tanpa password
        if ($user->isOAuthUser()) {
            return redirect()->route('account.settings')
                ->with('error', 'Akun Anda terhubung via Google. Perubahan kata sandi dikelola melalui akun Google Anda.');
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verifikasi password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('account.settings')
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    /**
     * Tambah rekening baru untuk Organizer.
     */
    public function storeRekening(Request $request)
    {
        $user = auth()->user();

        if (!$user->isOrganizer() && !$user->isSuperAdmin()) {
            abort(403, 'Hanya Organizer yang dapat menambahkan rekening.');
        }

        $validated = $request->validate([
            'tipe_rekening' => ['required', Rule::in(['Bank Transfer', 'E-Wallet'])],
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'nama_pemilik' => 'required|string|max:255',
        ]);

        // Jika belum punya rekening, otomatis jadikan primary
        $isPrimary = $user->rekening()->count() === 0;

        $user->rekening()->create(array_merge($validated, [
            'is_primary' => $isPrimary,
        ]));

        return redirect()->route('account.settings')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    /**
     * Set rekening sebagai primary (utama).
     */
    public function setPrimaryRekening($id)
    {
        $user = auth()->user();

        if (!$user->isOrganizer() && !$user->isSuperAdmin()) {
            abort(403);
        }

        // Reset semua rekening user menjadi non-primary
        $user->rekening()->update(['is_primary' => false]);

        // Set rekening terpilih menjadi primary
        $rekening = $user->rekening()->where('id_rekening', $id)->firstOrFail();
        $rekening->update(['is_primary' => true]);

        return redirect()->route('account.settings')
            ->with('success', 'Rekening "' . $rekening->nama_bank . '" telah dijadikan rekening utama.');
    }

    /**
     * Hapus rekening organizer.
     */
    public function destroyRekening($id)
    {
        $user = auth()->user();

        if (!$user->isOrganizer() && !$user->isSuperAdmin()) {
            abort(403);
        }

        $rekening = $user->rekening()->where('id_rekening', $id)->firstOrFail();
        $wasPrimary = $rekening->is_primary;
        $rekening->delete();

        // Jika yang dihapus primary, auto-promote rekening pertama
        if ($wasPrimary) {
            $first = $user->rekening()->first();
            if ($first) {
                $first->update(['is_primary' => true]);
            }
        }

        return redirect()->route('account.settings')
            ->with('success', 'Rekening berhasil dihapus.');
    }
}
