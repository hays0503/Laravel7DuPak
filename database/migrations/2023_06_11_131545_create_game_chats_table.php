<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ссылается на таблицу rooms
        // Перечень полей:
        // Id:              Идентификатор сообщения
        // Room_id:         Идентификатор комнаты в которой было отправлено сообщение 
        // Send_date:       Дата отправки сообщение
        // User_id:         Пользователь отправивший сообщение
        // Body_messages:   Тело сообщение
        Schema::create('game_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->dateTime('send_date');
            $table->unsignedBigInteger('user_id');
            $table->text('body_messages');

            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_chats');
    }
}