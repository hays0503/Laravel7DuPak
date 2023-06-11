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
        return $this->hasMany(Games::class);
    }



    public function create($name)
    {
        $this->name = $name;
        $this->create_data = now();
        $this->save();
        return $this;
    }
}