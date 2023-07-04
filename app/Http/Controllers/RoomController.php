<?php

namespace App\Http\Controllers;

use App\Games;
use App\GameChat;
use App\Room;
use App\User;
use Illuminate\Http\Request;
use App\RoomUser;

class RoomController extends Controller
{
    //Все комнаты
    public function getRoomsView()
    {
        $rooms = Room::all();
        $users = User::all();
        $lastRoomId = Room::max('id');

        return view('rooms', compact('rooms', 'users', 'lastRoomId'));
    }

    //Комната по id
    public function getRoom($Id)
    {
        // Возвращаем пользователей которые в комнате
        // Возвращаем название комнаты

        $room = Room::find($Id);
        // Текущий пользователь
        $user = auth()->user();
        //Пользователи в комнате
        $users = RoomUser::getUsersByGameId($Id);

        // Проверяем есть ли возможность человека зайти в комнату
        // Если нет, то редиректим на страницу с комнатами
        if ($room->users->contains($user)) {
            return view('Room', compact('room', 'users'));
        } else {
            // Отправляем сообщение об ошибке 'access-denied-in-room'
            return redirect(route('rooms'))->withErrors([
                'access-denied-in-room' => 'Нет доступа к комнате',
            ]);
        }
    }

    //Изменение состояния пользователя (is_ready) в конкретной комнате
    public function updateStateUser(Request $request)
    {
        $roomId = $request->route('id');
        error_log($roomId);
        $user_id = $request->input('user_id');
        error_log($user_id);
        $isReady = $request->input('is_ready');
        error_log($isReady);
        //Берем запись из таблицы room_user по id пользователя и id комнаты
        $roomUser = RoomUser::where('user_id', $user_id)->where('game_id', $roomId)->first();

        $roomUser->is_ready = $isReady;
        $roomUser->save();

        return response()->json([
            'success' => true,
            'user_in_room' => $roomUser
        ]);
    }

    //Запрос состояния пользователей (is_ready) в конкретной комнате
    public function getStateUser(Request $request)
    {
        $roomId = $request->route('id');
        error_log($roomId);
        $user_id = $request->input('user_id');
        //Проверяем есть ли запись в таблице room_user по id пользователя и id комнаты
        //(Состоит ли пользователь в комнате)
        $user_in_room = RoomUser::where('user_id', $user_id)->where('game_id', $roomId)->first();
        $roomsUser = RoomUser::where('game_id', $roomId)->get();
        if ($user_in_room == null) {
            //Status 403 - Forbidden
            return response()->json([
                'success' => false
            ], 403);
        } else {
            return response()->json([
                'success' => true,
                'user_in_room' => $roomsUser
            ], 200);
        }
    }




    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $room->name = $request->input('name');
        $room->save();

        $room->users()->sync($request->input('users'));

        return redirect()->route('rooms')->with('success', 'Комната успешно обновлена.');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        // Удалить связанные записи(сообщение пользователей) в таблице game_chat
        GameChat::deleteAllMessageInRoom($room->id);

        $room->users()->detach();
        $room->games()->delete(); // Удалить связанные записи в таблице "games"
        $room->delete(); // Удалить комнату

        return redirect()->route('rooms')->with('success', 'Комната успешно удалена.');
    }

    // RoomController.php
    public function create()
    {
        $users = User::all();
        return view('create_room', compact('users'));
    }

    //Вызывается из web.php при обращении к /rooms/create
    public function store(Request $request)
    {

        // Создаем новую комнату
        $room = new Room();
        $room->create($request->input('name'));

        // Связываем комнату с пользователями
        $room->users()->sync($request->input('users'));
        // Создаем новую игру
        // Заполнение таких данных как 
        // Id комнаты
        // Id пользователя создавшего комнату
        // Data лог хода игры резервируем пока пустым        
        $Games = new Games();
        // Создаем новую игру
        //null т.к нет еще победителя
        $Games->create("Начало игры;", null, auth()->user()->id, $room->id);


        // Перенаправляем на страницу со списком комнат и выводим сообщение
        return redirect()->route('rooms')->with('success', 'Комната успешно создана.');
    }
}