<?php

namespace App\Filament\Widgets;

use App\Models\Projek;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StokJelantahWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Jelantah — projek aktif';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $projek = Projek::aktif()->first();

        if ($projek === null) {
            return [
                Stat::make('Projek aktif', 'Belum ada')
                    ->description('Jadikan satu projek aktif di menu Projek.'),
            ];
        }

        $stok = $projek->stokJelantahKilogram();
        $literTerverifikasi = (float) $projek->setoran()->terverifikasi()->sum('jumlah_liter');

        return [
            Stat::make('Stok jelantah', $this->angka($stok).' kg')
                ->description($stok < 0
                    ? 'Stok negatif — periksa penjualan atau setoran yang belum diverifikasi.'
                    : 'Setoran terverifikasi dikurangi penjualan.')
                ->descriptionIcon($stok < 0 ? 'heroicon-m-exclamation-triangle' : null)
                ->color($stok < 0 ? 'danger' : 'primary'),
            Stat::make('Terkumpul', $this->angka($literTerverifikasi).' L')
                ->description('Setoran terverifikasi dalam projek ini.'),
            Stat::make('Hasil penjualan', 'Rp '.number_format($projek->totalPenjualanRupiah(), 0, ',', '.'))
                ->description($projek->penjualan()->count().' kali penjualan'),
        ];
    }

    private function angka(float $nilai): string
    {
        return number_format($nilai, 2, ',', '.');
    }
}
