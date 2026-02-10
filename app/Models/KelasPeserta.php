<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasPeserta extends Model
{
    protected $table = 'kelas_peserta';
    public $timestamps = false;

    protected $fillable = ['kelas_id', 'nama_peserta', 'email', 'no_telp'];

    protected $casts = ['created_at' => 'datetime'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
