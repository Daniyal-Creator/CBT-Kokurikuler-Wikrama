<?php

namespace App\Filament\Resources\Siswas\Tables;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nama')
            // Tanpa eager load ini, menampilkan kelas pada 700-an baris siswa
            // memicu satu kueri per baris.
            ->modifyQueryUsing(fn (Builder $query) => $query->with('penempatanAktif.kelas'))
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => '—',
                    }),
                TextColumn::make('penempatanAktif.kelas.nama')
                    ->label('Kelas')
                    ->badge()
                    ->placeholder('Belum ditempatkan'),
            ])
            ->filters([
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(fn (): array => Kelas::query()
                        ->whereRelation('tahunAjaran', 'aktif', true)
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $kelasId) => $query->whereHas(
                            'penempatanAktif',
                            fn (Builder $query) => $query->where('kelas_id', $kelasId),
                        ),
                    )),
                SelectFilter::make('belum_ditempatkan')
                    ->label('Penempatan')
                    ->options(['tanpa_kelas' => 'Belum ditempatkan'])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        ($data['value'] ?? null) === 'tanpa_kelas',
                        fn (Builder $query) => $query->whereDoesntHave('penempatanAktif'),
                    )),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada siswa')
            ->emptyStateDescription(
                TahunAjaran::aktif()->exists()
                    ? 'Tambahkan siswa satu per satu, atau impor dari Excel.'
                    : 'Tetapkan dulu tahun ajaran yang aktif sebelum menambahkan siswa.'
            );
    }
}
