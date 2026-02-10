<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRecord extends Model
{
    protected $table = 'returns';
    public $timestamps = false;

    protected $fillable = [
        'borrowing_id', 'tanggal_pengembalian', 'kondisi_barang', 'catatan', 'foto',
    ];

    protected $casts = [
        'tanggal_pengembalian' => 'date',
        'created_at' => 'datetime',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }
}
