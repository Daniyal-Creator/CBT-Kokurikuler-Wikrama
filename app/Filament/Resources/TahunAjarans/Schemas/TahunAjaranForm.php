<?php

namespace App\Filament\Resources\TahunAjarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TahunAjaranForm
{
    /**
     * Kolom `aktif` sengaja tidak ada di form.
     *
     * Keaktifan bersifat tunggal dan diubah lewat aksi "Jadikan aktif" pada
     * tabel, yang memadamkan tahun ajaran lain. Sebuah toggle biasa akan
     * memungkinkan dua tahun ajaran aktif bersamaan, dan "tahun ajaran yang
     * berlaku" menjadi tidak bermakna.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama')
                    ->placeholder('2025/2026')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal mulai')
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal selesai')
                    ->required()
                    ->after('tanggal_mulai'),
            ]);
    }
}
