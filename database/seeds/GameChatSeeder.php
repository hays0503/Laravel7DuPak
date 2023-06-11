<?php

use Illuminate\Database\Seeder;

class GameChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    // Заполнение таблицы game_chats
    public function SendLoremIpsumMessageInRoom($room_id, $user_id){
        // Отправляет сообщение в комнату (лорем ипсум)
        //Вызывая из модели функцию createMessage
        $gameChat = new App\GameChat;
        $gameChat->createMessage($room_id, $user_id, 'Lorem ipsum dolor sit amet, consectetur
         adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
    }

     
    public function run()
    {
        //
    }
}