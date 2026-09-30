<?php

namespace App\Filament\Resources\Setorans\Tables;

use App\Enums\StatusCatatan;
use App\Models\Projek;
use App\Models\Setoran;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SetoransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            // Kelas seorang penyetor diturunkan dari penempatannya. Tanpa muatan
            // awal ini, setiap baris memicu kueri sendiri.
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'siswa.penempatan.kelas',
                'projek',
            ]))
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('siswa.nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->state(fn (Setoran $record): string => $record->kelas()?->nama ?? 'Belum ditempatkan'),
                TextColumn::make('jumlah_liter')
                    ->label('Jumlah')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' L')
                    ->alignEnd()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('diverifikasiOleh.name')
                    ->label('Diverifikasi oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('projek_id')
                    ->label('Projek')
                    ->relationship('projek', 'nama')
                    ->default(fn () => Projek::aktif()->value('id'))
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusCatatan::class),
                Filter::make('menunggu_verifikasi')
                    ->label('Hanya yang menunggu verifikasi')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('status', StatusCatatan::Diajukan)),
            ])
            ->recordActions([
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Setoran $record): bool => $record->status !== StatusCatatan::Terverifikasi)
                    ->action(fn (Setoran $record) => $record->verifikasi(auth()->user())),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan penolakan')
                            ->maxLength(255),
                    ])
                    ->visible(fn (Setoran $record): bool => $record->status !== StatusCatatan::Ditolak)
                    ->action(fn (Setoran $record, array $data) => $record->tolak(auth()->user(), $data['alasan'] ?? null)),

                Action::make('batalkanVerifikasi')
                    ->label('Batalkan verifikasi')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->modalDescription('Catatan kembali berstatus diajukan dan keluar dari rekap. Perubahan ini tercatat pada jejak.')
                    ->visible(fn (Setoran $record): bool => $record->status === StatusCatatan::Terverifikasi)
                    ->action(fn (Setoran $record) => $record->batalkanVerifikasi(auth()->user())),

                EditAction::make(),
            ])
            ->toolbarActions([
                // Verifikasi satu per satu akan ditinggalkan guru dalam sepekan.
                // Borongan adalah syarat agar alur ini bertahan di lapangan.
                BulkAction::make('verifikasiTerpilih')
                    ->label('Verifikasi terpilih')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        $guru = auth()->user();
                        $jumlah = 0;

                        foreach ($records as $setoran) {
                            if ($setoran->status === StatusCatatan::Terverifikasi) {
                                continue;
                            }

                            $setoran->verifikasi($guru);
                            $jumlah++;
                        }

                        Notification::make()
                            ->title($jumlah.' setoran diverifikasi')
                            ->success()
                            ->send();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada setoran')
            ->emptyStateDescription('Catatan setoran jelantah akan muncul di sini setelah petugas menginputnya.');
    }
}
