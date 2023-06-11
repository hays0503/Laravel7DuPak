{{-- Реализация комнаты для игры в дурака --}}
{{-- При Нажатия на таблицу комнаты будет --}}
{{-- открыта эта вьюшка тем самым мы обозначаем вход в комнату --}}

<!DOCTYPE html>
<html lang="en">
{{-- Импортивуем стандартную шапку с панелью польхователя(логин/регистрация) --}}
@include('header')

<body>
    {{-- Здесь будет всплывающее окно с затемнением в котором будет отображенно информация --}}
    {{-- кто нажал на кнопку готов начать игру или еще не готов а так же есть чат --}}
    {{-- после того как все участники нажали на готово игра начинается --}}

    {{-- Кнопка для тестового вызова модального окна в дальнейшем будет убрана --}}
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">Large
        modal</button>
    {{-- Модальное окно (окно ожидание пользователей) --}}
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                {{-- Слева будет перечень игроков которые находятся в комнате --}}
                {{-- Справа  будет находится чат --}}
                {{-- Чат состоит из двух компонентов свехку история сообщений снизу строка ввода сообщения --}}
                {{-- Когда игрок набирает сообщение рядом с его именем будет появляться надпись "Пишет..." --}}
                {{-- Когда игрок нажимает на кнопку готов рядом с его именем появлятся зелёная галочка и надпись готов --}}

                <div class="container">
                    <div class="row">
                        
                            {{-- Список игроков --}}
                            <div class="col-2">
                                <div class="user-in-coom">
                                    @foreach (users as user)
                                        <div class="user-in-coom-item">
                                            <div class="user-in-coom-item-name">
                                                {{ $user->name }}
                                            </div>
                                            <div class="user-in-coom-item-status">
                                                {{-- Проверка на то что игрок готов --}}
                                                @if (user->ready == true)
                                                    <div class="user-in-coom-item-status-ready">
                                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        Готов
                                                    </div>
                                                @else
                                                    <div class="user-in-coom-item-status-not-ready">
                                                        <i class="fa fa-circle-o-notch" aria-hidden="true"></i>
                                                        Не готов
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

</body>

</html>
