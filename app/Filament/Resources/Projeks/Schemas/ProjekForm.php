<?php

namespace App\Filament\Resources\Projeks\Schemas;

use App\Models\TahunAjaran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjekForm
{
    /**
     * Kolom `aktif` tidak ada di sini, sama seperti pada Tahun Ajaran:
     * keaktifan tunggal dan diubah lewat aksi tersendiri.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tahun_ajaran_id')
                    ->label('Tahun ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->default(fn () => TahunAjaran::aktif()->value('id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama')
                    ->label('Nama projek')
                    ->placeholder('Gaya Hidup Berkelanjutan')
                    ->required()
                    ->maxLength(150),
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal mulai')
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal selesai')
                    ->required()
                    ->after('tanggal_mulai'),
                Select::make('hari_pengumpulan')
                    ->label('Hari pengumpulan jelantah')
                    ->helperText('Hanya untuk ditampilkan. Setoran bertanggal lain tetap diterima, karena kegiatan pengganti adalah hal biasa.')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                    ])
                    ->native(false),
                TextInput::make('faktor_konversi_liter_ke_kg')
                    ->label('Faktor konversi liter ke kilogram')
                    ->helperText('Jelantah dicatat dalam liter, tetapi dijual per kilogram. Umumnya sekitar 0,90.')
                    ->numeric()
                    ->minValue(0.1)
                    ->maxValue(2)
                    ->step(0.01)
                    ->default(0.90)
                    ->required(),
            ]);
    }
}
