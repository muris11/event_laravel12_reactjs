<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactDetail extends Model
{
    protected $table = 'contact_details';
    protected $fillable = ['contact_item_id', 'detail_text', 'detail_order'];

    public function contactItem(): BelongsTo
    {
        return $this->belongsTo(ContactItem::class, 'contact_item_id');
    }
}
