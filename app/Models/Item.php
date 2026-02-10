<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'nama_barang', 'kategori_id', 'tahun_perolehan', 'deskripsi', 'foto',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class, 'barang_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'barang_id');
    }
}
