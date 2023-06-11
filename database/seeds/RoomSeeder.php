<?php

use Illuminate\Database\Seeder;
use App\User;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */



    //Создание игры
    //Игра состоит из таблицы games, которая связывает таблицы rooms и users
    //Создание игры происходит в три этапа:
    //1. Создание записи в таблице rooms (комната)
    //2. Создание записи в таблице games (игра)
    //3. Создание записи в таблице room_user (связь комнаты и пользователя)
    public function create($name)
    {
        //Создаем игру
        //Шаг#1. Создает комнату        
        $AppRoom = new App\Room;
        $AppRoom->create($name);
    }

    public function run()
    {
        //Создаем комнату
        $this->create('room1');
        //Создаем комнату
        $this->create('room2');
        //Создаем комнату
        $this->create('room3');
    }
}