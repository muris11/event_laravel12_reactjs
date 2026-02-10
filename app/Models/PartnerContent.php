<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerContent extends Model
{
    protected $table = 'partner';
    protected $fillable = ['section', 'section_key', 'content_type', 'content_value'];
}
