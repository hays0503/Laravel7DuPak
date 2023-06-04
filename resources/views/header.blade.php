<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laravel</title>

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script src="{{ asset('js/app.js') }}"></script>

<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Игра в Дурака</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04"
            aria-controls="navbarsExample04" aria-expanded="true" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse show" id="navbarsExample04" style="">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Главное <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Рейтинг</a>
                </li>
            </ul>

            {{-- <div id='curren-user' class="auto  col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 curren-user"></div> --}}

            <form class="form-inline my-2 my-md-0">
                @auth
                    <div class="text-primary">{{ Auth::user()->email ?? '' }}</div>
                @endauth
                @guest
                    <a href="{{ route('user.registration') }}" class="ml-1 btn btn-primary">Зарегистрироваться</a>
                    <a href="{{ route('user.login') }}" class="ml-1 btn btn-primary">Войти</a>
                @endguest
                @auth
                    <a href="{{ route('user.logout') }}" class="ml-1 btn btn-danger">Выйти</a>
                @endauth
            </form>
        </div>
    </nav>
</header>
