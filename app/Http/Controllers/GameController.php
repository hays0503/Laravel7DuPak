<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RoomUser;


class GameController extends Controller
{
    public function GameRoom($id)
    {
        // Берем всех пользователей которые находятся в текущей комнате
        // $user = getUsersByGameId($id);
        return view('gameRoom', compact('id'));
    }
}