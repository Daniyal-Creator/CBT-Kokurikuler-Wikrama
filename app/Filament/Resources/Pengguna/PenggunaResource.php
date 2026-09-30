<?php

namespace App\Filament\Resources\Pengguna;

use App\Filament\Resources\Pengguna\Pages\CreatePengguna;
use App\Filament\Resources\Pengguna\Pages\EditPengguna;
use App\Filament\Resources\Pengguna\Pages\ListPengguna;
use App\Filament\Resources\Pengguna\Schemas\PenggunaForm;
use App\Filament\Resources\Pengguna\Tables\PenggunaTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PenggunaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $slug = 'pengguna';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Lapisan kedua di samping kebijakan model: rute data induk ditolak di
     * tingkat HTTP sebelum halaman sempat dimuat.
     */
    protected static string|array $routeMiddleware = 'peran:admin';

    public static function getNavigationBadge(): ?string
    {
        $menunggu = User::menungguPersetujuan()->count();

        return $menunggu > 0 ? (string) $menunggu : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pendaftar menunggu persetujuan';
    }

    public static function form(Schema $schema): Schema
    {
        return PenggunaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenggunaTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengguna::route('/'),
            'create' => CreatePengguna::route('/create'),
            'edit' => EditPengguna::route('/{record}/edit'),
        ];
    }
}
