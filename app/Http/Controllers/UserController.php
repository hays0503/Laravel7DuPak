<?php

namespace App\Http\Controllers;

use App\Room;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Показать список всех пользователей приложения.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        $users = User::all();

        return $users;
    }
}