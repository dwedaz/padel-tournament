<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function latestGame()
    {
        return $this->hasOne(Game::class)->latest();
    }
}
