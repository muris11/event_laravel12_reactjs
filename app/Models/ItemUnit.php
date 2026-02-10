<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemUnit extends Model
{
    protected $table = 'item_units';

    protected $fillable = ['barang_id', 'kode', 'kondisi', 'status'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'barang_id');
    }
}
