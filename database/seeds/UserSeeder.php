<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{

    public function createUser($name, $email, $password)
    {
        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->email_verified_at = now();
        $user->password = password_hash($password,'bcrypt');
        $user->save();

    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUser('Ваня', '1@gmail.com', '123');
        $this->createUser('Даня', '2@gmail.com', '123');
        $this->createUser('Гена', '3@gmail.com', '123');
        $this->createUser('Женя', '4@gmail.com', '123');
    }
}