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

    static public function getGameByRoomId($id)
    {
        return self::where('room_id', $id)->first();
    }

    //Является ли пользователь создателем игры(атрибут creator_id и room_id)
    static public function isCreatorGameRoom($id_user, $id_room)
    {
        $game = self::getGameByRoomId($id_room);
        if ($game->creator_id == $id_user) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteGame($id)
    {
        $game = $this->findOrFail($id);

        // Дополнительная логика перед удалением игры

        $game->delete();

        // Дополнительная логика после удаления игры
    }
}