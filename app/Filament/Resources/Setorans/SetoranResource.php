<?php

namespace App\Filament\Resources\Setorans;

use App\Filament\Resources\Setorans\Pages\CreateSetoran;
use App\Filament\Resources\Setorans\Pages\EditSetoran;
use App\Filament\Resources\Setorans\Pages\ListSetorans;
use App\Filament\Resources\Setorans\Schemas\SetoranForm;
use App\Filament\Resources\Setorans\Tables\SetoransTable;
use App\Models\Setoran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SetoranResource extends Resource
{
    protected static ?string $model = Setoran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Jelantah';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Setoran';

    protected static ?string $pluralModelLabel = 'Setoran Jelantah';

    protected static ?string $slug = 'setoran';

    public static function getNavigationBadge(): ?string
    {
        $menunggu = static::getModel()::menungguVerifikasi()->count();

        return $menunggu > 0 ? (string) $menunggu : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Setoran menunggu verifikasi';
    }

    public static function form(Schema $schema): Schema
    {
        return SetoranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SetoransTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSetorans::route('/'),
            'create' => CreateSetoran::route('/create'),
            'edit' => EditSetoran::route('/{record}/edit'),
        ];
    }
}
