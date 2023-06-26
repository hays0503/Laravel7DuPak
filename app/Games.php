<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    protected $fillable = ['name'];


    public function create($winner_id,$creator_id,$room_id)
    {
        $game = $this->create(
            ['data' => now()],
            ['winner_id' => $winner_id],
            ['creator_id' => $creator_id],
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