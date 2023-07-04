{{-- Чат --}}
<div>
    <div class="row m-0">
        <div class="col-9">
            <div>

                {{-- История сообщений загружаются с сервера посредством ajax запросов --}}
                <div class="justify-content-center  flex-column-reverse" id="chat-history">
                    {{-- Сообщение --}}



                </div>
                {{-- Кнопки для прокруткм в самый конец сообщений --}}
                <a id="last-messages">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                        class="bi bi-arrow-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z" />
                    </svg>
                </a>

            </div>

            <div class="flex-row">
                {{-- Поле ввода сообщения --}}
                <div class="d-flex flex-row" id="chat-input">
                    <div class="col-10">
                        <input type="text" class="form-control" id="message" placeholder="Сообщение">
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary" id="send-message">Отправить</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 bg-info">
            {{-- Список пользователей в комнете --}}
            <div class="flex-row">
                <div class="col-12">
                    <div class="flex-row">Пользователи в комнате:</div>
                </div>
                @foreach ($users as $user)
                    <div class="flex-row" id="userid-{{ $user->id }}">
                        <div class="col-12 bg-secondary" href="#">
                            <div class="flex-row">id: &nbsp;{{ $user->id }} </div>
                            <div class="flex-row">Имя: &nbsp;{{ $user->name }} </div>
                            <div class="flex-row">Email:&nbsp;{{ $user->email }} </div>
                            <div class="flex-row" id="is_ready">Готовность:</div><br>
                        </div>
                    </div>
                @endforeach
                <div class="p-2 bg-dark">
                    <button type="button" class="btn btn-block bg-success" id="ready">Готов</button>
                    <button type="button" class="btn btn-block bg-danger" id="not-ready">Не готов</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/chat.js') }}"></script>
    <script>
        //Обновление страницы
        setInterval(() => {
            //Обновление списка пользователей (Состояние готовности)
            update_user_info(
                {{ Auth::user()->id }},
                "{{ csrf_token() }}",
                "{{ route('rooms.get-state-user', ['id' => $room]) }}"
            );

            //Обновление истории сообщений
            update_chat_history(
                @json($users),
                "{{ route('chat.get-messages', ['id' => $room]) }}"
            );

        }, 2500);

        //Обработка нажатия на кнопку готов
        $("#ready").click(() => {
            set_state_user_ready(
                {{ Auth::user()->id }},
                "{{ csrf_token() }}",
                true,
                "{{ route('rooms.update-state-user', ['id' => $room]) }}")
        });
        //Обработка нажатия на кнопку не готов
        $("#not-ready").click(() => {
            set_state_user_ready(
                {{ Auth::user()->id }},
                "{{ csrf_token() }}",
                false,
                "{{ route('rooms.update-state-user', ['id' => $room]) }}")
        });

        /* Обработка отправки сообщение на сервер */
        $("#send-message").click(() => {
            send_message_user(
                {{ Auth::user()->id }},
                "{{ csrf_token() }}",
                "{{ route('chat.send-message', ['id' => $room]) }}"
            )
        });
        /* Отправка сообщения по нажатию enter*/
        $("#message").keypress((e) => {
            if (e.which == 13) {
                send_message_user(
                    {{ Auth::user()->id }},
                    "{{ csrf_token() }}",
                    "{{ route('chat.send-message', ['id' => $room]) }}"
                )
            }
        });


        /* Если в самом низу то скрываем кнопку скролла */
        $("#chat-history").scroll(() => {
            action_with_scroll()
        });

        /* Вешаем обработчик кнопки для прокруткм в самый конец сообщений */
        /* При нажатии на кнопку, скролим в самый низ */
        /* Скролл должен быть плавный */
        $("#last-messages").click(() => {
            let messages = document.getElementById("chat-history");
            messages.scrollTop = messages.scrollHeight;
        });
    </script>
</div>
