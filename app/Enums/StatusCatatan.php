<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusCatatan: string implements HasColor, HasLabel
{
    case Diajukan = 'diajukan';

    case Terverifikasi = 'terverifikasi';

    case Ditolak = 'ditolak';

    public function getLabel(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditolak => 'Ditolak',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::Diajukan => Color::Amber,
            self::Terverifikasi => Color::Green,
            self::Ditolak => Color::Red,
        };
    }

    /**
     * Hanya catatan terverifikasi yang boleh masuk rekap, stok, dan peringkat.
     */
    public function dihitung(): bool
    {
        return $this === self::Terverifikasi;
    }
}
