<?php

namespace App\Filament\Resources\Pengguna\Schemas;

use App\Enums\PeranPengguna;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenggunaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('peran')
                    ->label('Peran')
                    ->options(PeranPengguna::class)
                    ->default(PeranPengguna::Guru)
                    ->required()
                    ->native(false),

                // Kosong saat menyunting berarti kata sandi lama tetap berlaku.
                TextInput::make('password')
                    ->label('Kata sandi')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit'
                        ? 'Kosongkan bila tidak ingin mengganti kata sandi.'
                        : null),
            ]);
    }
}
