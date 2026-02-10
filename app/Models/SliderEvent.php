<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderEvent extends Model
{
    protected $table = 'slider_events';
    protected $fillable = ['selected_events'];
    protected $casts = ['selected_events' => 'array'];
}
