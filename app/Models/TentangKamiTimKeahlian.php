<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TentangKamiTimKeahlian extends Model
{
    protected $table = 'tentang_kami_tim_keahlian';
    public $timestamps = false;

    protected $fillable = ['tim_id', 'keahlian', 'urutan'];

    public function tim(): BelongsTo
    {
        return $this->belongsTo(TentangKamiTim::class, 'tim_id');
    }
}
