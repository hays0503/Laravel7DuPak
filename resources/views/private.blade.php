<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('header')

<body class="antialiased">
    <div class="container text-center MainWidget">
        <h1 class="align-self-center text-light">Страница авторизации=>Приватная часть</h1>
        <form class="bg-primary col-3 offset-4 border rounded" method="GET" action="{{ route('user.logout') }}">
            <button class="btn btn-lg btn-primary" type="submit" name="sendMe" value="1">Выйти</button>
        </form>
    </div>

</body>

</html>
