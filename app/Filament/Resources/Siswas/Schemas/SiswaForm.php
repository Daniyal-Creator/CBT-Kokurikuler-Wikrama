<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true),
                TextInput::make('nama')
                    ->label('Nama lengkap')
                    ->required()
                    ->maxLength(120),
                Select::make('jenis_kelamin')
                    ->label('Jenis kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->native(false),
            ]);
    }
}
