<?php

namespace App\Http\Controllers;

use App\User;


class UserController extends Controller
{
//    /**
//    * Показать список всех пользователей приложения.
//    *
//    * @return Response
//    */
//   public function index()
//   {
//     $users = DB::table('users')->get();

//     return view('user.index', ['users' => $users]);
//   }

  public function getUsers()
  {
    $users = User::all();

    return view('rooms', ['users' => $users]);
  }  
}