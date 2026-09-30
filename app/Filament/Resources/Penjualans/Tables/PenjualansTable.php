<?php

namespace App\Filament\Resources\Penjualans\Tables;

use App\Models\Projek;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PenjualansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('pembeli')
                    ->label('Pembeli')
                    ->searchable(),
                TextColumn::make('jumlah_kg')
                    ->label('Jumlah')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->alignEnd()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')->numeric(decimalPlaces: 2)->suffix(' kg')),
                TextColumn::make('harga_per_kg')
                    ->label('Harga/kg')
                    ->money('IDR', locale: 'id')
                    ->alignEnd(),
                TextColumn::make('total_rupiah')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')->money('IDR', locale: 'id')),
                TextColumn::make('dicatatOleh.name')
                    ->label('Dicatat oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('projek_id')
                    ->label('Projek')
                    ->relationship('projek', 'nama')
                    ->default(fn () => Projek::aktif()->value('id'))
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada penjualan')
            ->emptyStateDescription('Catat di sini setiap kali jelantah dijual ke pengepul.');
    }
}
