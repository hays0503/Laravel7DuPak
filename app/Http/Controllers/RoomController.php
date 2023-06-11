<?php

namespace App\Http\Controllers;

use App\Games;
use App\Room;
use App\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function getRooms()
    {
        $rooms = Room::all();
        $users = User::all();
        $lastRoomId = Room::max('id');

        return view('rooms', compact('rooms', 'users', 'lastRoomId'));
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

        // Перенаправляем на страницу со списком комнат и выводим сообщение
        return redirect()->route('rooms')->with('success', 'Комната успешно создана.');
    }
}