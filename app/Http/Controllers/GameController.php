<?php

namespace App\Http\Controllers;

use App\RoomUser;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function GameRoom($id)
    {
        // Берем всех пользователей которые находятся в текущей комнате
        $room_users = RoomUser::getUsersByGameId($id);
        return view(
            'gameRoom',
            ['id' => 'id', 'room_users' => 'room_users']

        );
    }
}