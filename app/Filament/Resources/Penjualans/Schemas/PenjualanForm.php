<?php

namespace App\Filament\Resources\Penjualans\Schemas;

use App\Models\Projek;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PenjualanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('projek_id')
                    ->label('Projek')
                    ->relationship('projek', 'nama')
                    ->default(fn () => Projek::aktif()->value('id'))
                    ->required()
                    ->preload()
                    ->live()
                    ->helperText(function (Get $get): ?string {
                        $projek = Projek::find($get('projek_id'));

                        if ($projek === null) {
                            return null;
                        }

                        return 'Stok saat ini: '.number_format($projek->stokJelantahKilogram(), 2, ',', '.').' kg (dari setoran terverifikasi).';
                    }),

                DatePicker::make('tanggal')
                    ->label('Tanggal penjualan')
                    ->default(now())
                    ->required(),

                TextInput::make('pembeli')
                    ->label('Pembeli / pengepul')
                    ->helperText('Tidak ditampilkan di halaman publik.')
                    ->required()
                    ->maxLength(150),

                TextInput::make('jumlah_kg')
                    ->label('Jumlah (kg)')
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->suffix('kg')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::isiTotal($get, $set)),

                TextInput::make('harga_per_kg')
                    ->label('Harga per kg')
                    ->helperText('Tidak ditampilkan di halaman publik.')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::isiTotal($get, $set)),

                TextInput::make('total_rupiah')
                    ->label('Total diterima')
                    ->helperText('Terisi otomatis dari jumlah × harga. Ubah bila pengepul membulatkan pembayaran.')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->required(),

                Textarea::make('catatan')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    /**
     * Total hanya disarankan, tidak dikunci: uang yang benar-benar diterima
     * adalah kebenaran yang dicatat, dan pengepul lazim membulatkannya.
     */
    private static function isiTotal(Get $get, Set $set): void
    {
        $jumlahKg = (float) $get('jumlah_kg');
        $hargaPerKg = (int) $get('harga_per_kg');

        if ($jumlahKg <= 0 || $hargaPerKg <= 0) {
            return;
        }

        $set('total_rupiah', (int) round($jumlahKg * $hargaPerKg));
    }
}
