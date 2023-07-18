<?php

namespace App\Http\Controllers;

use App\RoomUser;
use App\Games;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function GameRoom($id)
    {
        // Берем всех пользователей которые находятся в текущей комнате
        // $room_users = RoomUser::getUsersByGameId($id);
        return view(
            'GameRoom',
            ['id' => $id]

        );
    }

    //Обновляем текущего пользователя и его действие
    public function UpdateCurrentUserAction(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_game = $request->input('id_game');
        //Берем пользователей в комнате
        $room_users = RoomUser::getUsersByGameId($id_game);
        
        //Устанавливаем следующего пользователя
        //Алгоритм: Делаем список сортируем его по id
        //Ищем индекс текущего пользователя в списке
        //Увеличиваем индекс на 1
        //Если индекс больше длины списка, то обнуляем индекс
        //Берем пользователя по индексу
        $room_users_list = $room_users->sortBy('id');
        $current_user_index = $room_users_list->search(function ($user, $key) use ($id_user) {
            return $user->id == $id_user;
        });
        $next_user_index = $current_user_index + 1;
        if ($next_user_index >= $room_users_list->count()) {
            $next_user_index = 0;
        }
        $next_user = $room_users_list->get($next_user_index);
        $next_user_id = $next_user->id;
        //Обновляем текущего пользователя
        Games::updateCurrentUserActionId($id_game, $next_user_id);
        
        return response()->json(['room_users'=>$room_users]);
        
    }

    
    //Получаем текущего пользователя и его действие
    public function GetCurrentUserAction($id)
    {
        $room_users = RoomUser::getUsersByGameId($id);
        $current_user_action_id = Games::getGameByRoomId($id);
        
        return ['current_user_action_id'=>$current_user_action_id->current_user_action_id,
                'seed'=>$current_user_action_id->seed,
                'winner_id'=>$current_user_action_id->winner_id,
                'current_user'=>Auth::user()->id,];
    }
}