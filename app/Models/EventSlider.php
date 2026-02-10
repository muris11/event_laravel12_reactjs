<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSlider extends Model
{
    protected $table = 'event_slider';

    protected $fillable = [
        'image_url', 'description', 'order_position', 'is_active',
        'orientasi', 'width', 'height', 'crop_mode',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
