<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranLari;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Runner: menampilkan semua pendaftaran, E-Ticket, QR Code, dan BIB Number.
     */
    public function runnerDashboard()
    {
        $pendaftaran = PendaftaranLari::where('id_runner', auth()->user()->id_user)
            ->with(['event', 'kategori', 'pembayaran'])
            ->latest()
            ->get();

        return view('runner.dashboard', compact('pendaftaran'));
    }
}
