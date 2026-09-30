<?php

namespace App\Models;

use Database\Factories\TahunAjaranFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    /** @use HasFactory<TahunAjaranFactory> */
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function penempatanSiswa(): HasMany
    {
        return $this->hasMany(PenempatanSiswa::class);
    }

    #[Scope]
    protected function aktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Menandai tahun ajaran ini sebagai satu-satunya yang aktif.
     *
     * Keaktifan bersifat tunggal: menyalakan yang satu memadamkan yang lain.
     * Tanpa ini, "tahun ajaran yang berlaku" menjadi ambigu dan seluruh
     * rekap kelas ikut ambigu.
     */
    public function jadikanAktif(): void
    {
        static::query()->whereKeyNot($this->getKey())->update(['aktif' => false]);

        $this->forceFill(['aktif' => true])->save();
    }
}
