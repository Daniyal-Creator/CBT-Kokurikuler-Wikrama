<?php

namespace App\Filament\Resources\TahunAjarans;

use App\Filament\Resources\TahunAjarans\Pages\CreateTahunAjaran;
use App\Filament\Resources\TahunAjarans\Pages\EditTahunAjaran;
use App\Filament\Resources\TahunAjarans\Pages\ListTahunAjarans;
use App\Filament\Resources\TahunAjarans\Schemas\TahunAjaranForm;
use App\Filament\Resources\TahunAjarans\Tables\TahunAjaransTable;
use App\Models\TahunAjaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Tahun Ajaran';

    protected static ?string $pluralModelLabel = 'Tahun Ajaran';

    protected static ?string $slug = 'tahun-ajaran';

    /**
     * Lapisan kedua di samping kebijakan model: rute data induk ditolak di
     * tingkat HTTP sebelum halaman sempat dimuat.
     */
    protected static string|array $routeMiddleware = 'peran:admin';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return TahunAjaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunAjaransTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTahunAjarans::route('/'),
            'create' => CreateTahunAjaran::route('/create'),
            'edit' => EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
