<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Показывает форму входа пользователя
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('user.private');
        }

        return view('login');
    }

    /**
     * Обрабатывает запрос на вход пользователя
     */
    public function login(Request $request)
    {
        $formFields = $request->only(['email', 'password']);

        if (Auth::check()) {
            return redirect()->intended(route('user.private'));
        }

        if (Auth::attempt($formFields)) {
            return redirect()->intended(route('user.private'));
        }

        return redirect(route('user.login'))->withErrors([
            'email' => 'Не удалось авторизоваться',
        ]);
    }

    /**
     * Выполняет выход пользователя
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Авторизует пользователя после успешной регистрации
     */
    protected function loginUser($user)
    {
        Auth::login($user);
    }
}