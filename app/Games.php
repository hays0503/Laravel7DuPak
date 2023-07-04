<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{

    public function create($data, $winner_id, $creator_id, $room_id)
    {
        $this->data = $data;
        $this->winner_id = $winner_id;
        $this->creator_id = $creator_id;
        $this->room_id = $room_id;
        $this->save();

        return $this->id;
    }

    public function deleteGame($id)
    {
        $game = $this->findOrFail($id);

        // Дополнительная логика перед удалением игры

        $game->delete();

        // Дополнительная логика после удаления игры
    }
}