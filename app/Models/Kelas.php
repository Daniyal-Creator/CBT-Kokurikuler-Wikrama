<?php

namespace App\Models;

use Database\Factories\KelasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    /** @use HasFactory<KelasFactory> */
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'tingkat',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function penempatanSiswa(): HasMany
    {
        return $this->hasMany(PenempatanSiswa::class);
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'penempatan_siswa')
            ->withTimestamps();
    }
}
