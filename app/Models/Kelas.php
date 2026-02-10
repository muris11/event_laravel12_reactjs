<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas', 'kategori_id', 'deskripsi', 'jadwal', 'ruangan',
        'biaya', 'total_peserta', 'foto', 'gambaran_event',
        'link_navigasi', 'is_link_eksternal',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'is_link_eksternal' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function tiketKategori(): HasMany
    {
        return $this->hasMany(TiketKategori::class, 'kelas_id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(KelasPeserta::class, 'kelas_id');
    }
}
