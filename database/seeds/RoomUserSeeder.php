<?php

use App\RoomUser;
use Illuminate\Database\Seeder;

class RoomUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roomUser = new RoomUser();
        $roomUser->addUserToRoom(1, 1);
        $roomUser->addUserToRoom(2, 1);
        $roomUser->addUserToRoom(3, 1);
    }
}