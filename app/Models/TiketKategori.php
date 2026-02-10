<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketKategori extends Model
{
    protected $table = 'tiket_kategori';

    protected $fillable = [
        'kelas_id', 'nama_kategori', 'deskripsi', 'harga',
        'manfaat', 'is_populer', 'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_populer' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
