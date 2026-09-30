<?php

namespace App\Filament\Resources\Projeks\Tables;

use App\Models\Projek;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjeksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_mulai', 'desc')
            ->columns([
                TextColumn::make('nama')
                    ->label('Projek')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun ajaran')
                    ->sortable(),
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('hari_pengumpulan')
                    ->label('Hari pengumpulan')
                    ->formatStateUsing(fn (?int $state, Projek $record): string => $record->namaHariPengumpulan() ?? '—'),
                TextColumn::make('setoran_count')
                    ->label('Setoran')
                    ->counts('setoran')
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
                    ->modalHeading('Jadikan projek ini yang berjalan?')
                    ->modalDescription('Projek yang sedang aktif akan dinonaktifkan. Catatan lama tidak dihapus dan tetap dapat dilihat.')
                    ->visible(fn (Projek $record): bool => ! $record->aktif)
                    ->action(fn (Projek $record) => $record->jadikanAktif()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
