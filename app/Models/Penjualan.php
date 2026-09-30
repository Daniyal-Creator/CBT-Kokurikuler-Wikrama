<?php

namespace App\Models;

use Database\Factories\PenjualanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sisi "keluar" dari Jelantah: sekolah melepasnya kepada pengepul.
 *
 * Penjualan bukan Catatan — tidak ada verifikasi, karena yang mencatatnya
 * sudah Guru atau Admin sendiri.
 */
class Penjualan extends Model
{
    /** @use HasFactory<PenjualanFactory> */
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'projek_id',
        'tanggal',
        'pembeli',
        'jumlah_kg',
        'harga_per_kg',
        'total_rupiah',
        'catatan',
        'dicatat_oleh_user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_kg' => 'decimal:2',
            'harga_per_kg' => 'integer',
            'total_rupiah' => 'integer',
        ];
    }

    public function projek(): BelongsTo
    {
        return $this->belongsTo(Projek::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }
}
