{{-- Чат --}}
<style>
    #chat-history {
        overflow-y: auto;
        overflow-x: hidden;
        display: flex-reverse;
        flex-direction: column-reverse;
        scroll-behavior: smooth;

        @media screen and (max-height: 1366px) {
            height: 90vh;
            max-height: 95vh;
        }


        @media screen and (max-height: 665px) {
            height: 85vh;
            max-height: 85vh;
        }

    }

    #last-messages {
        position: absolute;

        @media screen and (max-height: 665px) {
            top: 80vh;
            left: 55vw;
        }

        @media screen and (max-height: 1366px) {
            top: 80vh;
            left: 65vw;
        }
    }

    @keyframes colorChangeTimer {
        0% {
            fill: #fbff01
        }

        50% {
            fill: #000
        }

        100% {
            fill: #e5ff00
        }
    }

    .colorChangeTimerSVG {
        width: 200px;
        height: 200px;
        animation: colorChangeTimer 3s infinite;
    }
    }
</style>
<div>
    <div class="row m-0">
        <div class="col-9">
            <div>
                {{-- История сообщений загружаются с сервера посредством ajax запросов --}}
                <div class="justify-content-center  flex-column-reverse" id="chat-history">
                    {{-- Сообщение --}}

                    {{-- Кнопки для прокруткм в самый конец сообщений --}}
                    <a id="last-messages">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                            class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z" />
                        </svg>
                    </a>

                </div>

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
            <script>
                /* Вешаем обработчик кнопки для прокруткм в самый конец сообщений */
                /* При нажатии на кнопку, скролим в самый низ */
                /* Скролл должен быть плавный */
                $("#last-messages").click(function() {
                    let messages = document.getElementById('chat-history');
                    messages.scrollTop = messages.scrollHeight;
                });

                /* Если в самом низу то скрываем кнопку скролла */
                $("#chat-history").scroll(function() {
                    let messages = document.getElementById('chat-history');
                    let messagesHeight = messages.scrollHeight;
                    let messagesScrollTop = messages.scrollTop;
                    let messagesClientHeight = messages.clientHeight;
                    if (messagesHeight - messagesScrollTop - messagesClientHeight < 20) {
                        $("#last-messages").hide();
                    } else {
                        $("#last-messages").show();
                    }
                });

                const element = document.getElementById('chat-history');

                // Создаем экземпляр ResizeObserver с функцией обратного вызова
                const observer = new ResizeObserver(function(entries) {
                    // Обработка события изменения размеров элемента
                    console.log('Div был перерисован из-за изменения размеров');
                    const element = document.getElementById('chat-history');
                    const hasVerticalScrollbar = element.scrollHeight > element.clientHeight;
                    if (!hasVerticalScrollbar) {
                        /* Вертикальная полоса прокрутки отсутствует */
                        $("#last-messages").hide();
                    }
                });

                // Начинаем отслеживать изменения размеров элемента
                observer.observe(element);

                /* Обработка отправки сообщение на сервер */
                $("#send-message").click(function() {
                    let messageData = $("#message").val();
                    console.log(messageData);
                    if (messageData.length > 0) {
                        $.ajax({
                            url: "{{ route('chat.send-message', ['id' => $room]) }}",
                            type: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "user_id": {{ Auth::user()->id }},
                                message: messageData,
                            },
                            success: function(data) {
                                $("#message").val("");
                            },
                            error: function(data) {
                                console.log(data);
                            }
                        });
                    }
                });




                /* Обрабочка получение сообщений с сервера и добавление их.*/
                $.ajax({
                    url: "{{ route('chat.get-messages', ['id' => $room]) }}",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        /*[
                            {
                                "id": 2,
                                "room_id": 1,
                                "send_date": "2023-06-17 20:50:08",
                                "user_id": 1,
                                "body_messages": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus magnam ullam, ab eveniet molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi voluptatibus corrupti nemo harum soluta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus magnam ullam, ab eveniet molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi voluptatibus corrupti nemo harum soluta.",
                                "created_at": "2023-06-17T20:50:08.000000Z",
                                "updated_at": "2023-06-17T20:50:08.000000Z"
                            },
                            {
                                "id": 3,
                                "room_id": 1,
                                "send_date": "2023-06-17 20:50:08",
                                "user_id": 2,
                                "body_messages": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus magnam ullam, ab eveniet molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi voluptatibus corrupti nemo harum soluta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus magnam ullam, ab eveniet molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi voluptatibus corrupti nemo harum soluta.",
                                "created_at": "2023-06-17T20:50:08.000000Z",
                                "updated_at": "2023-06-17T20:50:08.000000Z"
                            }
                        ]*/

                        /* 
                        <div class="d-flex flex-row" id="messages-1">
                                    {{-- Cмотри тэг скрипт --}}
                                    <div class="col">
                                        <div class="row">
                                            <a href="#">Автор</a>
                                        </div>
                                        <div class="row">
                                            00:00:01
                                        </div>
                                    </div>

                                    <div class="col-11" id="message-body">
                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus magnam ullam,
                                        ab
                                        eveniet
                                        molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi voluptatibus
                                        corrupti
                                        nemo
                                        harum soluta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ducimus
                                        magnam
                                        ullam,
                                        ab
                                        eveniet molestiae, rerum sint ipsa quas at debitis enim iusto dolores excepturi
                                        voluptatibus
                                        corrupti
                                        nemo harum soluta.

                                    </div>
                                </div>
                                 */

                        console.log(data);
                        let messageHtml = "";
                        const userjson = @json($users);
                        console.log(userjson)

                        data.forEach(element => {
                            /* Создаем блок сообщения */
                            let messageBlock = document.createElement('div');
                            messageBlock.className = 'd-flex flex-row';
                            messageBlock.id = 'messages-' + element.id;

                            /* Создаем блок автора */
                            let authorBlock = document.createElement('div');
                            authorBlock.className = 'col';

                            /* Создаем блок имени автора */
                            let authorNameBlock = document.createElement('div');
                            authorNameBlock.className = 'row';

                            /* Создаем ссылку на автора */
                            let authorLink = document.createElement('a');
                            authorLink.href = '#';

                            authorLink.innerHTML = userjson.find((user) => {
                                return user.id == element.user_id;
                            }).name;
                            console.log(element.user_id)

                            /* Создаем блок времени отправки */
                            let authorTimeBlock = document.createElement('div');
                            authorTimeBlock.className = 'row';
                            authorTimeBlock.innerHTML = element.send_date;

                            /* Добавляем ссылку в блок имени автора */
                            authorNameBlock.append(authorLink);

                            /* Добавляем блок имени и времени в блок автора */
                            authorBlock.append(authorNameBlock);
                            authorBlock.append(authorTimeBlock);

                            /* Создаем блок тела сообщения */
                            let messageBodyBlock = document.createElement('div');
                            messageBodyBlock.className = 'col-11';
                            messageBodyBlock.id = 'message-body';
                            messageBodyBlock.innerHTML = element.body_messages;

                            /* Добавляем блок автора и тела сообщения в блок сообщения */
                            messageBlock.append(authorBlock);
                            messageBlock.append(messageBodyBlock);

                            /* Добавляем блок сообщения в общий блок с сообщениями */
                            let messages = document.getElementById('chat-history');
                            messages.append(messageBlock);

                            /* Добавляем скролл в самый низ */
                            messages.scrollTop = messages.scrollHeight;
                        });
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            </script>
        </div>
        <div class="col-3 bg-info">
            {{-- Список пользователей в комнете --}}
            @foreach ($users as $user)
                <div class="flex-row" id="userid-{{ $user->id }}">
                    <div class="col-12 bg-secondary" href="#">
                        <div class="flex-row">Имя: &nbsp;{{ $user->name }} </div>
                        <div class="flex-row">Email:&nbsp;{{ $user->email }} </div>
                        <div class="flex-row" id="is_ready">Готовность:
                            @if ($user->is_ready)
                                <span style="color:rgb(0, 255, 0)">
                                    Готов &nbsp;
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                        <path
                                            d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z" />
                                        <path
                                            d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z" />
                                    </svg>
                                </span>
                            @else
                                <span style="color:rgb(217, 255, 0)">
                                    Не готов &nbsp;
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path class="colorChangeTimerSVG"
                                            d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                        <path class="colorChangeTimerSVG"
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                    </svg>
                                </span>
                        </div>
            @endif
            </h5>
        </div>
    </div>
    @endforeach
    <div class="p-2 bg-dark">
        <button type="button" class="btn btn-block bg-success" id="ready">Готов</button>
        <button type="button" class="btn btn-block bg-danger" id="not-ready">Не готов</button>
    </div>
    <script>
        $("#ready").click(function() {
            $.ajax({
                url: "{{ route('rooms.update-state-user', ['id' => $room]) }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "user_id": {{ Auth::user()->id }},
                    "is_ready": true
                },
                success: function(data) {
                    console.log(data);
                    /* Запрос успешен меняем статус  */
                    if (data?.success) {
                        const id_user = data?.user_in_room.user_id;
                        const is_ready = data?.is_ready;
                        const element = document.getElementById('userid-' + id_user);
                        const element_is_ready = element.querySelector('#is_ready');
                        element_is_ready.innerHTML = `
                            Готовность:
                            <span style="color:rgb(0, 255, 0)">
                                Готов &nbsp;
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                    <path
                                        d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z" />
                                    <path
                                        d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z" />
                                </svg>
                            </span>
                        `;
                    }
                }
            });
        });

        $("#not-ready").click(function() {
            $.ajax({
                url: "{{ route('rooms.update-state-user', ['id' => $room]) }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "user_id": {{ Auth::user()->id }},
                    "is_ready": false
                },
                success: function(data) {
                    console.log(data);
                    console.log(data);
                    /* Запрос успешен меняем статус  */
                    if (data?.success) {
                        const id_user = data?.user_in_room.user_id;
                        const is_ready = data?.is_ready;
                        const element = document.getElementById('userid-' + id_user);
                        const element_is_ready = element.querySelector('#is_ready');
                        element_is_ready.innerHTML = `
                            Готовность:
                            <span style="color:rgb(217, 255, 0)">
                                Не готов &nbsp;
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                    <path class="colorChangeTimerSVG"
                                        d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                    <path class="colorChangeTimerSVG"
                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                </svg>
                            </span>
                            `;
                    }
                }
            });
        });
    </script>
</div>
</div>
</div>
