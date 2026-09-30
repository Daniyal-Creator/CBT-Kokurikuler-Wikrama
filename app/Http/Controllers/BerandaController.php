<?php

namespace App\Http\Controllers;

use App\Models\Projek;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Halaman publik tanpa login.
 *
 * Hanya agregat yang boleh keluar dari sini: tidak ada nama siswa, nama
 * pengepul, atau harga per kilogram (PRD §6).
 */
class BerandaController extends Controller
{
    public function __invoke(): View
    {
        $projek = Projek::aktif()->first();

        if ($projek === null) {
            return view('beranda', ['projek' => null]);
        }

        $papanPeringkat = $projek->papanPeringkatKelas();

        return view('beranda', [
            'projek' => $projek,
            'papanPeringkat' => $papanPeringkat,
            'totalLiter' => $papanPeringkat->sum('total_liter'),
            'totalRupiah' => $projek->totalPenjualanRupiah(),
            'totalKgTerjual' => $projek->totalPenjualanKilogram(),
            'pengumpulanBerikutnya' => $this->pengumpulanBerikutnya($projek),
        ]);
    }

    /**
     * Hari pengumpulan hanya keterangan (PRD §5.4). Bila hari ini adalah hari
     * pengumpulan, yang ditampilkan adalah hari ini.
     */
    private function pengumpulanBerikutnya(Projek $projek): ?Carbon
    {
        if ($projek->hari_pengumpulan === null) {
            return null;
        }

        $hariIni = Carbon::today();

        if ($hariIni->dayOfWeekIso === $projek->hari_pengumpulan) {
            return $hariIni;
        }

        return $hariIni->next($projek->hari_pengumpulan % 7);
    }
}
