<?php

namespace App\Filament\Resources\Kelas\Tables;

use App\Models\TahunAjaran;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nama')
            ->columns([
                TextColumn::make('nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->sortable(),
                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun ajaran')
                    ->sortable(),
                TextColumn::make('siswa_count')
                    ->label('Jumlah siswa')
                    ->counts('siswa')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->default(fn () => TahunAjaran::aktif()->value('id'))
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
