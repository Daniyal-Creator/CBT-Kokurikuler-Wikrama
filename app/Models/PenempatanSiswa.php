<?php

namespace App\Models;

use Database\Factories\PenempatanSiswaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class PenempatanSiswa extends Model
{
    /** @use HasFactory<PenempatanSiswaFactory> */
    use HasFactory;

    protected $table = 'penempatan_siswa';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
    ];

    /**
     * `tahun_ajaran_id` tidak pernah diisi dari luar.
     *
     * Kolom itu adalah salinan dari kelas yang ditempati, dan ada semata-mata
     * agar basis data dapat menjamin satu siswa hanya menempati satu kelas per
     * tahun ajaran. Mengisinya manual membuka peluang salinan itu berbeda dari
     * sumbernya — persis kebohongan yang hendak dicegah.
     */
    protected static function booted(): void
    {
        static::saving(function (self $penempatan): void {
            $kelas = $penempatan->kelas()->first();

            if ($kelas === null) {
                throw new RuntimeException('Penempatan siswa harus menunjuk ke sebuah kelas.');
            }

            $penempatan->tahun_ajaran_id = $kelas->tahun_ajaran_id;
        });
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
