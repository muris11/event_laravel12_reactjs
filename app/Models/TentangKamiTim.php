<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TentangKamiTim extends Model
{
    protected $table = 'tentang_kami_tim';

    protected $fillable = ['nama', 'jabatan', 'deskripsi', 'foto', 'urutan', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function keahlian(): HasMany
    {
        return $this->hasMany(TentangKamiTimKeahlian::class, 'tim_id');
    }
}
