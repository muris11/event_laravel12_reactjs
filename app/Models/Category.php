<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['nama'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'kategori_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'kategori_id');
    }
}
