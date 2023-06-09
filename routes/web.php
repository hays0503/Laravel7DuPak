<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'login');

Route::name('user.')->group(function () {
    Route::view('/private', 'private')->middleware('auth')->name('private');

    Route::get('/login', 'AuthController@showLoginForm')->name('login');
    Route::post('/login', 'AuthController@login');

    Route::get('/logout', 'AuthController@logout')->name('logout');

    Route::get('/registration', 'RegistrationController@showRegistrationForm')->name('registration');
    Route::post('/registration', 'RegistrationController@save');


});

Route::get('/rooms', 'RoomController@getRooms')->name('rooms');

Route::put('/rooms/{id}', 'RoomController@update')->name('rooms.update');

Route::delete('/rooms/{id}', 'RoomController@destroy')->name('rooms.destroy');

Route::get('/rooms/create', 'RoomController@create')->name('rooms.create');

Route::post('/rooms', 'RoomController@store')->name('rooms.store');

Route::get('/game', function () {
    return view('welcome');
});