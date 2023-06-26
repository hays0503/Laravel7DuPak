<?php

use Illuminate\Database\Seeder;

use App\Games;

class createAnyGames extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function create($winner_id, $creator_id, $room_id)
    {
        //Шаг#2. Создает игру
        $AppGames = new Games;
        $AppGames->create($winner_id->id, $creator_id->id, $room_id->id);
        $AppGames->save();
    }



    public function run()
    {
        //Шаг#2. Создает игру #1
        $this->create(1,1,1);

    }
}