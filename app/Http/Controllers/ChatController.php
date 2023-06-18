<?php

namespace App\Http\Controllers;

use App\GameChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Получите данные из запроса, включая сообщение и id отправителя
        // Сохраните сообщение в базе данных, связав его с комнатой $id
        // Верните ответ клиенту, например, JSON-ответ с подтверждением
        $ModelMessages = new GameChat;
        $roomId = $request->route('id');
        $user_id = $request->input('user_id');
        $body_messages = $request->input('message');
        $ModelMessages->createMessage($roomId, $user_id, $body_messages);

        return response()->json([
            'message' => 'Message sent successfully!'
        ], 201);
    }

    public function getMessages(Request $request)
    {
        // Извлеките сообщения из базы данных, связанные с комнатой $id
        // Верните сообщения клиенту, например, в формате JSON
        $ModelMessages = new GameChat;
        $roomId = $request->route('id');
        $Messages = $ModelMessages->messageInRoom($roomId);
        return $Messages;
    }

}