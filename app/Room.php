<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;

    protected $table = 'rooms';

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user', 'game_id', 'user_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}