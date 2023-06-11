<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $id)
    {
        // Получите данные из запроса, включая сообщение и id отправителя
        // Сохраните сообщение в базе данных, связав его с комнатой $id
        // Верните ответ клиенту, например, JSON-ответ с подтверждением
    }

    public function getMessages($id)
    {
        // Извлеките сообщения из базы данных, связанные с комнатой $id
        // Верните сообщения клиенту, например, в формате JSON
    }

}