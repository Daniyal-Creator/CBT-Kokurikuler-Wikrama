<?php

namespace App\Models;

use App\Enums\StatusCatatan;
use Database\Factories\SetoranFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Setoran extends Model
{
    /** @use HasFactory<SetoranFactory> */
    use HasFactory;

    protected $table = 'setoran';

    protected $fillable = [
        'projek_id',
        'siswa_id',
        'tanggal',
        'jumlah_liter',
        'dicatat_oleh_user_id',
    ];

    /**
     * Kolom yang menyatakan hasil verifikasi sengaja tidak dapat diisi massal.
     * Perpindahan status hanya terjadi lewat verifikasi(), tolak(), dan
     * batalkanVerifikasi(), sehingga jejaknya tidak dapat dilewati.
     *
     * @var list<string>
     */
    protected $guarded = [
        'status',
        'diverifikasi_oleh_user_id',
        'diverifikasi_pada',
        'alasan_penolakan',
    ];

    /**
     * Mencerminkan nilai bawaan kolom di basis data, agar sebuah Setoran yang
     * baru dibuat sudah menyatakan statusnya sebelum tersimpan.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => StatusCatatan::Diajukan->value,
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_liter' => 'decimal:2',
            'status' => StatusCatatan::class,
            'diverifikasi_pada' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $setoran): void {
            $setoran->catatJejakKoreksi();
        });
    }

    public function projek(): BelongsTo
    {
        return $this->belongsTo(Projek::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh_user_id');
    }

    public function jejakPerubahan(): MorphMany
    {
        return $this->morphMany(JejakPerubahan::class, 'catatan');
    }

    #[Scope]
    protected function terverifikasi(Builder $query): Builder
    {
        return $query->where('status', StatusCatatan::Terverifikasi);
    }

    #[Scope]
    protected function menungguVerifikasi(Builder $query): Builder
    {
        return $query->where('status', StatusCatatan::Diajukan);
    }

    public function jumlahKilogram(): float
    {
        return $this->projek->literKeKilogram((float) $this->jumlah_liter);
    }

    /**
     * Kelas yang menerima kontribusi ini.
     *
     * Diturunkan dari penempatan siswa pada tahun ajaran milik projek — bukan
     * disimpan — agar tidak ada dua kebenaran tentang kelas seorang murid.
     */
    public function kelas(): ?Kelas
    {
        // Sengaja menelusuri koleksi, bukan membangun kueri baru: bila relasi
        // sudah dimuat lebih dulu, menampilkan kelas pada ratusan baris tidak
        // menambah satu kueri pun.
        return $this->siswa
            ->penempatan
            ->firstWhere('tahun_ajaran_id', $this->projek->tahun_ajaran_id)
            ?->kelas;
    }

    public function verifikasi(User $guru): void
    {
        $this->forceFill([
            'status' => StatusCatatan::Terverifikasi,
            'diverifikasi_oleh_user_id' => $guru->getKey(),
            'diverifikasi_pada' => now(),
            'alasan_penolakan' => null,
        ])->save();
    }

    public function tolak(User $guru, ?string $alasan = null): void
    {
        $this->forceFill([
            'status' => StatusCatatan::Ditolak,
            'diverifikasi_oleh_user_id' => $guru->getKey(),
            'diverifikasi_pada' => now(),
            'alasan_penolakan' => $alasan,
        ])->save();
    }

    public function batalkanVerifikasi(User $guru): void
    {
        $this->forceFill([
            'status' => StatusCatatan::Diajukan,
            'diverifikasi_oleh_user_id' => null,
            'diverifikasi_pada' => null,
        ])->save();
    }

    /**
     * Mencatat perubahan atas catatan yang sudah terverifikasi.
     *
     * Verifikasi kehilangan maknanya bila angka dapat berubah diam-diam
     * sesudahnya. Koreksi tetap boleh, asalkan meninggalkan jejak.
     */
    protected function catatJejakKoreksi(): void
    {
        // getRawOriginal, bukan getOriginal: kolom ini di-cast menjadi enum,
        // sehingga getOriginal mengembalikan objek dan perbandingan dengan
        // string selalu gagal — jejak pun tidak pernah tertulis.
        if ($this->getRawOriginal('status') !== StatusCatatan::Terverifikasi->value) {
            return;
        }

        foreach (['jumlah_liter', 'tanggal', 'siswa_id', 'status'] as $kolom) {
            if (! $this->isDirty($kolom)) {
                continue;
            }

            $this->jejakPerubahan()->create([
                'user_id' => auth()->id(),
                'kolom' => $kolom,
                'nilai_lama' => $this->formatNilaiJejak($this->getOriginal($kolom)),
                'nilai_baru' => $this->formatNilaiJejak($this->getAttribute($kolom)),
            ]);
        }
    }

    protected function formatNilaiJejak(mixed $nilai): ?string
    {
        return match (true) {
            $nilai === null => null,
            $nilai instanceof StatusCatatan => $nilai->value,
            $nilai instanceof \DateTimeInterface => $nilai->format('Y-m-d'),
            default => (string) $nilai,
        };
    }
}
