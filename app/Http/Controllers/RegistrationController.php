<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    /**
     * Показывает форму регистрации.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('registration');
    }

    public function save(Request $request)
    {
        // Проверяем, если пользователь уже авторизован, то перенаправляем на приватную страницу
        if (Auth::check()) {
            return redirect(route('user.private'));
        }

        // Валидируем поля email и password из запроса
        $validateField = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Проверяем, если пользователь с таким email уже существует, возвращаем ошибку
        if (User::where('email', $validateField['email'])->exists()) {
            return redirect(route('user.registration'))->withErrors([
                'email' => 'Произошла ошибка, пользователь существует',
            ]);
        }

        // Хешируем пароль перед сохранением
        $hashedPassword = Hash::make($validateField['password']);

        // Создаем нового пользователя с переданными данными
        $user = User::create([
            'name' => $validateField['name'],
            'email' => $validateField['email'],
            'password' => $hashedPassword,
        ]);

        // Если пользователь успешно создан, выполняем его авторизацию
        if ($user) {
            Auth::login($user);
            return redirect(route('user.private'));
        }

        // Если происходит какая-либо ошибка, возвращаем ошибку
        return redirect(route('user.private'))->withErrors([
            'formError' => 'Произошла ошибка',
        ]);
    }
}