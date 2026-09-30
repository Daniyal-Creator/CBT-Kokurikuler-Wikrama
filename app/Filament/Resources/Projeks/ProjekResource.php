<?php

namespace App\Filament\Resources\Projeks;

use App\Filament\Resources\Projeks\Pages\CreateProjek;
use App\Filament\Resources\Projeks\Pages\EditProjek;
use App\Filament\Resources\Projeks\Pages\ListProjeks;
use App\Filament\Resources\Projeks\Schemas\ProjekForm;
use App\Filament\Resources\Projeks\Tables\ProjeksTable;
use App\Models\Projek;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjekResource extends Resource
{
    protected static ?string $model = Projek::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Projek';

    protected static ?string $pluralModelLabel = 'Projek';

    protected static ?string $slug = 'projek';

    /**
     * Lapisan kedua di samping kebijakan model: rute data induk ditolak di
     * tingkat HTTP sebelum halaman sempat dimuat.
     */
    protected static string|array $routeMiddleware = 'peran:admin';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return ProjekForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjeksTable::configure($table);
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
            'index' => ListProjeks::route('/'),
            'create' => CreateProjek::route('/create'),
            'edit' => EditProjek::route('/{record}/edit'),
        ];
    }
}
