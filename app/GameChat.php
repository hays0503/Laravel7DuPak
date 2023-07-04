<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GameChat extends Model
{
    // Модель оперирует данными из бд

    // Возвращает id созданного сообщения
    // @arg $room_id - id комнаты в которой будет создано сообщение
    // @arg $user_id - id пользователя который отправил сообщение
    // @arg $body_messages - Тело сообщения
    public function createMessage($room_id, $user_id, $body_messages)
    {
        $this->room_id = $room_id;
        $this->send_date = now();
        $this->user_id = $user_id;
        $this->body_messages = $body_messages;
        $this->save();
        return $this->id;
    }

    // Возвращает пользователей которые находятся в комнате
    public function userInRooms($room_id)
    {
        return $this->select('user_id')->where('room_id', $room_id)->get();
    }

    // Возвращает комнату в которой находится пользователь и последний раз отправлял сообщение
    public function lastRooms($user_id)
    {
        // Сортировка по id в обратном порядке (
        // Так как id увеличивается с каждым добавлением записи, то последняя запись будет иметь самый большой id
        return $this->select('room_id')->where('user_id', $user_id)->orderBy('id', 'desc')->first();
    }

    // Возвращает сообщения которые находятся в комнате
    public function messageInRoom($room_id)
    {
        return $this->where('room_id', $room_id)->get();
    }

    //Удалить все сообщения в комнате
    static public function deleteAllMessageInRoom($room_id)
    {
        return DB::table('game_chats')->where('room_id', $room_id)->delete();
    }
    

}