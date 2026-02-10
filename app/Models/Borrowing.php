<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrowing extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'user_id', 'nama_peminjam', 'barang_id', 'unit_kode',
        'tanggal_pinjam', 'tanggal_kembali', 'keperluan', 'status',
        'alasan_penolakan', 'tanggal_verifikasi', 'deleted_at',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_verifikasi' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'barang_id');
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(ReturnRecord::class, 'borrowing_id');
    }
}
