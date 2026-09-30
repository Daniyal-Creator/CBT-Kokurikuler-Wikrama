<?php

namespace App\Models;

use Database\Factories\ProjekFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Projek extends Model
{
    /** @use HasFactory<ProjekFactory> */
    use HasFactory;

    protected $table = 'projek';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'hari_pengumpulan',
        'faktor_konversi_liter_ke_kg',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'hari_pengumpulan' => 'integer',
            'faktor_konversi_liter_ke_kg' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    #[Scope]
    protected function aktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function jadikanAktif(): void
    {
        static::query()->whereKeyNot($this->getKey())->update(['aktif' => false]);

        $this->forceFill(['aktif' => true])->save();
    }

    /**
     * Jelantah yang sudah diterima, dinyatakan dalam kilogram.
     *
     * Liter adalah satuan yang dicatat manusia; kilogram adalah satuan yang
     * dipakai pengepul. Konversi terjadi di sini dan hanya di sini, sehingga
     * tidak ada dua angka yang dapat saling bertentangan.
     */
    public function literKeKilogram(float $liter): float
    {
        return round($liter * (float) $this->faktor_konversi_liter_ke_kg, 2);
    }

    /**
     * Jelantah yang sudah diterima sekolah tetapi belum dijual, dalam kilogram.
     *
     * Selalu dihitung, tidak pernah diketik. Hasil negatif sengaja dibiarkan
     * negatif: itu tanda ada Setoran belum terverifikasi atau Penjualan salah
     * input, dan membulatkannya ke nol hanya menyembunyikan kesalahan itu.
     */
    public function stokJelantahKilogram(): float
    {
        $masukLiter = (float) $this->setoran()->terverifikasi()->sum('jumlah_liter');
        $keluarKg = (float) $this->penjualan()->sum('jumlah_kg');

        return round($this->literKeKilogram($masukLiter) - $keluarKg, 2);
    }

    /**
     * Urutan Kelas menurut liter Jelantah terverifikasi dalam projek ini.
     *
     * Kelas seorang penyetor diambil dari penempatannya pada tahun ajaran
     * projek, bukan tahun ajaran yang sedang berjalan — agar peringkat projek
     * lama tidak berubah ketika murid naik kelas. Kelas tanpa setoran tetap
     * tampil dengan nol liter: kelas yang hilang dari papan tidak terdorong
     * untuk mulai menyetor.
     *
     * Rata-rata per murid hanya pendamping, bukan dasar urutan (PRD §5.6).
     *
     * @return Collection<int, array{kelas: string, total_liter: float, jumlah_murid: int, rata_rata_liter: float}>
     */
    public function papanPeringkatKelas(): Collection
    {
        $literTerverifikasi = Setoran::query()
            ->terverifikasi()
            ->where('projek_id', $this->getKey())
            ->whereIn('siswa_id', PenempatanSiswa::query()
                ->select('siswa_id')
                ->whereColumn('penempatan_siswa.kelas_id', 'kelas.id'))
            ->selectRaw('coalesce(sum(jumlah_liter), 0)');

        return Kelas::query()
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->withCount('penempatanSiswa as jumlah_murid')
            ->addSelect(['total_liter' => $literTerverifikasi])
            ->orderByDesc('total_liter')
            ->orderBy('nama')
            ->get()
            ->map(function (Kelas $kelas): array {
                $totalLiter = round((float) $kelas->total_liter, 2);
                $jumlahMurid = (int) $kelas->jumlah_murid;

                return [
                    'kelas' => $kelas->nama,
                    'total_liter' => $totalLiter,
                    'jumlah_murid' => $jumlahMurid,
                    'rata_rata_liter' => $jumlahMurid > 0 ? round($totalLiter / $jumlahMurid, 2) : 0.0,
                ];
            });
    }

    public function totalPenjualanRupiah(): int
    {
        return (int) $this->penjualan()->sum('total_rupiah');
    }

    public function totalPenjualanKilogram(): float
    {
        return round((float) $this->penjualan()->sum('jumlah_kg'), 2);
    }

    public function namaHariPengumpulan(): ?string
    {
        return match ($this->hari_pengumpulan) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => null,
        };
    }
}
