<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PendaftaranLari extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_lari';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'id_event',
        'id_runner',
        'id_kategori',
        'bib_number',
        'ukuran_jersey',
        'qr_code_token',
        'status_pembayaran',
        'status_racepack',
        'is_diwakilkan',
        'nama_pengambil',
        'nik_pengambil',
        'catatan_rpc',
        'waktu_pengambilan_racepack',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pengambilan_racepack' => 'datetime',
            'is_diwakilkan' => 'boolean',
        ];
    }

    /* ================================================================
     * RELASI ELOQUENT
     * ================================================================ */

    /**
     * Pendaftaran milik satu event.
     */
    public function event()
    {
        return $this->belongsTo(EventLari::class, 'id_event', 'id_event');
    }

    /**
     * Pendaftaran milik satu runner (User).
     */
    public function runner()
    {
        return $this->belongsTo(User::class, 'id_runner', 'id_user');
    }

    /**
     * Pendaftaran termasuk satu kategori lari.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriLari::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Pendaftaran memiliki satu data pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasOne(PembayaranLari::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    /* ================================================================
     * HELPER METHODS
     * ================================================================ */

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'Lunas';
    }

    public function isSudahDiambil(): bool
    {
        return $this->status_racepack === 'Sudah Diambil';
    }

    /**
     * Generate prefix BIB berdasarkan nama kategori.
     */
    public static function generateBibPrefix(?string $namaKategori): string
    {
        $namaKategori = $namaKategori ?? '';
        if (preg_match('/(\d+K)/i', $namaKategori, $matches)) {
            return strtoupper($matches[1]);
        }
        if (stripos($namaKategori, 'half marathon') !== false) {
            return 'HM';
        }
        if (stripos($namaKategori, 'marathon') !== false) {
            return 'FM';
        }
        $words  = explode(' ', trim($namaKategori));
        $prefix = '';
        foreach ($words as $word) {
            if ($word !== '') {
                $prefix .= strtoupper(substr($word, 0, 1));
            }
        }
        return $prefix ?: 'RF';
    }

    /**
     * Generate dan terbitkan nomor BIB resmi untuk pendaftaran ini jika belum memiliki BIB.
     * Menggunakan DB::transaction dan lockForUpdate() agar aman dari race condition
     * ketika banyak transaksi webhook Midtrans masuk bersamaan.
     */
    public function assignBibNumber(): string
    {
        if (!empty($this->bib_number)) {
            return $this->bib_number;
        }

        return DB::transaction(function () {
            // Lock record pendaftaran ini
            $pendaftaran = self::where('id_pendaftaran', $this->id_pendaftaran)
                ->lockForUpdate()
                ->first();

            if (!empty($pendaftaran->bib_number)) {
                $this->bib_number = $pendaftaran->bib_number;
                return $pendaftaran->bib_number;
            }

            $kategori = $pendaftaran->kategori;
            $prefix   = self::generateBibPrefix($kategori?->nama_kategori);

            // Lock record BIB di event ini untuk prefix yang sama agar nomor urut konsisten
            $maxBib = self::where('id_event', $pendaftaran->id_event)
                ->whereNotNull('bib_number')
                ->where('bib_number', 'like', $prefix . '-%')
                ->lockForUpdate()
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(bib_number, '-', -1) AS UNSIGNED)) as max_num")
                ->value('max_num');

            $nextNumber = ($maxBib ?? 0) + 1;
            $bibNumber  = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $pendaftaran->update(['bib_number' => $bibNumber]);
            $this->bib_number = $bibNumber;

            return $bibNumber;
        });
    }

    /* ================================================================
     * GOOGLE CALENDAR ACCESSORS
     * ================================================================ */

    /**
     * Menghasilkan URL Google Calendar untuk Hari-H Lomba.
     * Format: All-day event pada tanggal_event.
     */
    public function getGoogleCalendarRaceDayUrlAttribute(): ?string
    {
        $event = $this->event;
        if (!$event || !$event->tanggal_event) {
            return null;
        }

        $namaEvent = $event->nama_event ?? 'Event Lari';
        $kategori  = $this->kategori->nama_kategori ?? '';
        $bib       = $this->bib_number ?? '-';
        $lokasi    = $event->lokasi_venue ?? '';

        // All-day event: gunakan format YYYYMMDD (tanpa waktu)
        $startDate = $event->tanggal_event->format('Ymd');
        // End date untuk all-day event harus +1 hari (Google Calendar convention)
        $endDate   = $event->tanggal_event->copy()->addDay()->format('Ymd');

        $title   = "🏃 {$namaEvent} — Hari Lomba";
        $details = "Event: {$namaEvent}\n"
                 . "Kategori: {$kategori}\n"
                 . "BIB Number: {$bib}\n"
                 . "Lokasi: {$lokasi}\n\n"
                 . "Jangan lupa bawa perlengkapan lomba dan racepack Anda!\n"
                 . "Powered by RunFest SaaS";

        $params = http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => $title,
            'dates'    => "{$startDate}/{$endDate}",
            'details'  => $details,
            'location' => $lokasi,
            'sf'       => 'true',
        ]);

        return 'https://calendar.google.com/calendar/render?' . $params;
    }

    /**
     * Menghasilkan URL Google Calendar untuk periode Pengambilan Racepack (RPC).
     * Format: All-day event dari tanggal_rpc_mulai s/d tanggal_rpc_selesai.
     */
    public function getGoogleCalendarRpcUrlAttribute(): ?string
    {
        $event = $this->event;
        if (!$event || !$event->tanggal_rpc_mulai) {
            return null;
        }

        $namaEvent = $event->nama_event ?? 'Event Lari';
        $kategori  = $this->kategori->nama_kategori ?? '';
        $bib       = $this->bib_number ?? '-';
        $lokasi    = $event->lokasi_venue ?? '';
        $jersey    = $this->ukuran_jersey ?? '-';

        // Periode RPC: mulai s/d selesai (atau +1 hari jika selesai tidak diset)
        $startDate = $event->tanggal_rpc_mulai->format('Ymd');
        $endDate   = $event->tanggal_rpc_selesai
            ? $event->tanggal_rpc_selesai->copy()->addDay()->format('Ymd')
            : $event->tanggal_rpc_mulai->copy()->addDay()->format('Ymd');

        $title   = "📦 Racepack Collection — {$namaEvent}";
        $details = "Event: {$namaEvent}\n"
                 . "Kategori: {$kategori}\n"
                 . "BIB Number: {$bib}\n"
                 . "Ukuran Jersey: {$jersey}\n"
                 . "Lokasi: {$lokasi}\n\n"
                 . "📋 Checklist Pengambilan:\n"
                 . "☑ Bawa KTP/KIA/Kartu Pelajar Asli\n"
                 . "☑ Tunjukkan QR Code E-Ticket\n"
                 . "⚠ Jika diwakilkan: Surat Kuasa + KTP Perwakilan\n\n"
                 . "Powered by RunFest SaaS";

        $params = http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => $title,
            'dates'    => "{$startDate}/{$endDate}",
            'details'  => $details,
            'location' => $lokasi,
            'sf'       => 'true',
        ]);

        return 'https://calendar.google.com/calendar/render?' . $params;
    }
}
