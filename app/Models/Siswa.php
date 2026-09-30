<?php

namespace App\Models;

use Database\Factories\SiswaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    /** @use HasFactory<SiswaFactory> */
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
    ];

    public function penempatan(): HasMany
    {
        return $this->hasMany(PenempatanSiswa::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'penempatan_siswa')
            ->withTimestamps();
    }

    /**
     * Penempatan siswa ini pada tahun ajaran yang sedang berlaku.
     *
     * Ada sebagai relasi—bukan hasil perhitungan—agar daftar siswa dapat
     * menampilkan kelasnya tanpa satu kueri tambahan per baris.
     */
    public function penempatanAktif(): HasOne
    {
        return $this->hasOne(PenempatanSiswa::class)
            ->whereRelation('tahunAjaran', 'aktif', true);
    }

    /**
     * Kelas yang ditempati siswa ini pada suatu Tahun Ajaran.
     *
     * Sengaja tidak ada kolom `kelas_id` pada siswa: seorang murid menempati
     * kelas yang berbeda tiap tahun, dan rekap tahun lalu harus tetap benar
     * setelah ia naik kelas.
     */
    public function kelasPada(TahunAjaran $tahunAjaran): ?Kelas
    {
        return $this->penempatan()
            ->where('tahun_ajaran_id', $tahunAjaran->getKey())
            ->with('kelas')
            ->first()?->kelas;
    }
}
