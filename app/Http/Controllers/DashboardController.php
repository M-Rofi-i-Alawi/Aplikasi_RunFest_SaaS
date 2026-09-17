<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranLari;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Runner: menampilkan pendaftaran, E-Ticket, QR Code, dan BIB Number,
     * dipisahkan menjadi Tiket Aktif dan Riwayat Event Selesai.
     */
    public function runnerDashboard()
    {
        $userId = auth()->user()->id_user;

        $pendaftaran = PendaftaranLari::where('id_runner', $userId)
            ->with(['event', 'kategori', 'pembayaran'])
            ->latest()
            ->get();

        $today = now()->startOfDay();

        // 1. Tiket Aktif: Event yang statusnya bukan 'SELESAI' dan tanggal pelaksanaan lombanya masih di masa depan (atau hari ini).
        $activeTickets = $pendaftaran->filter(function ($item) use ($today) {
            if (!$item->event) {
                return false;
            }
            $isFinished = strtoupper($item->event->status_event ?? '') === 'SELESAI'
                || ($item->event->tanggal_event && $item->event->tanggal_event->startOfDay()->lt($today));

            return !$isFinished;
        });

        // 2. Riwayat Event Selesai: Event yang statusnya sudah 'SELESAI' atau tanggal pelaksanaan lombanya sudah lewat.
        $historyTickets = $pendaftaran->filter(function ($item) use ($today) {
            if (!$item->event) {
                return false;
            }
            $isFinished = strtoupper($item->event->status_event ?? '') === 'SELESAI'
                || ($item->event->tanggal_event && $item->event->tanggal_event->startOfDay()->lt($today));

            return $isFinished && $item->status_pembayaran === 'Lunas';
        });

        return view('runner.dashboard', compact('pendaftaran', 'activeTickets', 'historyTickets'));
    }
}

