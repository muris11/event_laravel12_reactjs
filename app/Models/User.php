<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'username', 'password', 'role', 'nama_lengkap',
        'email', 'no_telepon', 'alamat', 'foto_profil', 'last_login',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }
}
