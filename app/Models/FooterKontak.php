<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterKontak extends Model
{
    protected $table = 'footer_kontak';

    protected $fillable = [
        'email', 'phone', 'address', 'description', 'copyright_text',
        'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube',
    ];
}
