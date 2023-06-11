<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    protected $fillable = ['name'];


    public function create($room_id)
    {
        $game = $this->create(
            ['data' => now()],
            ['winner_id' => null],
            ['room_id' => $room_id]
        );
        // Дополнительная логика создания игры

        return $game;
    }

    public function deleteGame($id)
    {
        $game = $this->findOrFail($id);

        // Дополнительная логика перед удалением игры

        $game->delete();

        // Дополнительная логика после удаления игры
    }
}