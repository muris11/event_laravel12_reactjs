<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactItem extends Model
{
    protected $table = 'contact_items';
    protected $fillable = ['icon', 'title', 'action_url', 'order_position', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function details(): HasMany
    {
        return $this->hasMany(ContactDetail::class, 'contact_item_id');
    }
}
