<?php

namespace App\Filament\Resources\TahunAjarans\Tables;

use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TahunAjaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_mulai', 'desc')
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('kelas_count')
                    ->label('Jumlah kelas')
                    ->counts('kelas')
                    ->alignEnd(),
                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('jadikanAktif')
                    ->label('Jadikan aktif')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->modalHeading('Jadikan tahun ajaran ini yang berlaku?')
                    ->modalDescription('Tahun ajaran yang sedang aktif akan dinonaktifkan. Data tahun sebelumnya tidak dihapus dan tetap dapat dilihat.')
                    ->visible(fn (TahunAjaran $record): bool => ! $record->aktif)
                    ->action(fn (TahunAjaran $record) => $record->jadikanAktif()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
