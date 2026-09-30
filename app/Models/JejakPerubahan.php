<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JejakPerubahan extends Model
{
    protected $table = 'jejak_perubahan';

    /**
     * Jejak tidak pernah diubah setelah ditulis, sehingga `updated_at`
     * tidak punya arti dan tidak disediakan.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'kolom',
        'nilai_lama',
        'nilai_baru',
    ];

    public function catatan(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
