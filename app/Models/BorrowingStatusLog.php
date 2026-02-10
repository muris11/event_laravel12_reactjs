<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingStatusLog extends Model
{
    protected $table = 'borrowing_status_log';
    public $timestamps = false;

    protected $fillable = ['borrowing_id', 'status_lama', 'status_baru', 'updated_by'];

    protected $casts = ['created_at' => 'datetime'];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }
}
