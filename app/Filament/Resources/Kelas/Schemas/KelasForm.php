<?php

namespace App\Filament\Resources\Kelas\Schemas;

use App\Models\TahunAjaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class KelasForm
{
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
                    ->label('Nama kelas')
                    ->placeholder('XI RPL 1')
                    ->required()
                    ->maxLength(30)
                    // Nama kelas hanya unik di dalam tahun ajarannya: "XI RPL 1"
                    // tahun depan adalah kelas lain dengan murid yang lain.
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Rule $rule, callable $get) => $rule->where('tahun_ajaran_id', $get('tahun_ajaran_id')),
                    ),
                TextInput::make('tingkat')
                    ->label('Tingkat')
                    ->placeholder('XI')
                    ->maxLength(10),
            ]);
    }
}
