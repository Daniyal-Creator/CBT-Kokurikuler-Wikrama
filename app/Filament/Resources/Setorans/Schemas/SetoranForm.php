<?php

namespace App\Filament\Resources\Setorans\Schemas;

use App\Models\Projek;
use App\Models\Siswa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SetoranForm
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
                    ->preload(),

                // Petugas mencari murid lewat NIS saat hafal, lewat nama saat
                // tidak. Keduanya harus bekerja dalam satu kotak yang sama.
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->placeholder('Ketik NIS atau nama')
                    ->required()
                    ->searchable(['nis', 'nama'])
                    ->getSearchResultsUsing(fn (string $search): array => Siswa::query()
                        ->where('nis', 'like', $search.'%')
                        ->orWhere('nama', 'like', '%'.$search.'%')
                        ->orderBy('nama')
                        ->limit(30)
                        ->get()
                        ->mapWithKeys(fn (Siswa $siswa): array => [
                            $siswa->id => $siswa->nis.' — '.$siswa->nama,
                        ])
                        ->all())
                    ->getOptionLabelUsing(function ($value): ?string {
                        $siswa = Siswa::find($value);

                        return $siswa === null ? null : $siswa->nis.' — '.$siswa->nama;
                    }),

                DatePicker::make('tanggal')
                    ->label('Tanggal setor')
                    ->default(now())
                    ->required(),

                TextInput::make('jumlah_liter')
                    ->label('Jumlah (liter)')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(999)
                    ->step(0.01)
                    ->required()
                    ->suffix('L'),
            ]);
    }
}
