<?php

namespace App\Filament\Resources\Pengguna\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenggunaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Pendaftar yang menunggu diletakkan paling atas, karena merekalah
            // alasan Admin membuka halaman ini.
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByRaw('disetujui_pada is not null'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('peran')
                    ->label('Peran')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => $record->sudahDisetujui() ? 'Aktif' : 'Menunggu persetujuan')
                    ->color(fn (User $record): string => $record->sudahDisetujui() ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('menunggu_persetujuan')
                    ->label('Hanya yang menunggu persetujuan')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNull('disetujui_pada')),
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Akun ini akan dapat masuk panel dan memverifikasi catatan sebagai Guru.')
                    ->visible(fn (User $record): bool => ! $record->sudahDisetujui())
                    ->action(function (User $record): void {
                        $record->setujui();

                        Notification::make()
                            ->title($record->name.' disetujui')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->emptyStateHeading('Belum ada pengguna');
    }
}
