<?php

use Illuminate\Database\Seeder;

class createAnyGames extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function create($room_id)
    {
        //Шаг#2. Создает игру
        $AppGames = new App\Games;
        $AppGames->create($room_id->id);
    }



    public function run()
    {
        //Шаг#2. Создает игру #1
        $this->create(1);

        //Шаг#2. Создает игру #2
        $this->create(2);

        //Шаг#2. Создает игру #3
        $this->create(3);
    }
}