function action_with_scroll() {
    let messages = document.getElementById("chat-history");
    let messagesHeight = messages.scrollHeight;
    let messagesScrollTop = messages.scrollTop;
    let messagesClientHeight = messages.clientHeight;
    if (messagesHeight - messagesScrollTop - messagesClientHeight < 20) {
        $("#last-messages").hide();
    } else {
        $("#last-messages").show();
    }
}

// Создаем экземпляр ResizeObserver с функцией обратного вызова
const observer = new ResizeObserver(function (entries) {
    // Обработка события изменения размеров элемента
    console.log("Div был перерисован из-за изменения размеров");
    const element = document.getElementById("chat-history");
    const hasVerticalScrollbar = element.scrollHeight > element.clientHeight;
    if (!hasVerticalScrollbar) {
        /* Вертикальная полоса прокрутки отсутствует */
        $("#last-messages").hide();
    }
});

// Начинаем отслеживать изменения размеров элемента(для прокрутки в самый конец сообщений)
observer.observe(document.getElementById("chat-history"));

function send_message_user(user_id, csrf_token, url_send_message) {
    let messageData = $("#message").val();
    console.log(messageData);
    if (messageData.length > 0) {
        $.ajax({
            url: url_send_message,
            type: "POST",
            data: {
                _token: csrf_token,
                user_id: user_id,
                message: messageData,
            },
            success: function (data) {
                $("#message").val("");
            },
            error: function (data) {
                console.log(data);
            },
        });
    }
}

/* Раз в 5 секунд опрашиваем сервер на новые сообщение */
/* Обрабочка получение сообщений с сервера и добавление их.*/

function update_chat_history(userjson, url_get_messages) {
    $.ajax({
        url: url_get_messages,
        type: "GET",
        dataType: "json",
        success: function (data) {
            let messageHtml = "";
            console.log("chat.get-messages", data);
            const messages = data.messages;
            if (messages != []) {
                // Очистить элемент chat-history
                let chatHistory = document.getElementById("chat-history");
                chatHistory.innerHTML = "";

                messages?.map((element) => {
                    /* Создаем блок сообщения */
                    let messageBlock = document.createElement("div");
                    messageBlock.className = "d-flex flex-row";
                    messageBlock.id = "messages-" + element.id;

                    /* Создаем блок автора */
                    let authorBlock = document.createElement("div");
                    authorBlock.className = "col";

                    /* Создаем блок имени автора */
                    let authorNameBlock = document.createElement("div");
                    authorNameBlock.className = "row";

                    /* Создаем ссылку на автора */
                    let authorLink = document.createElement("a");
                    authorLink.href = "#";

                    authorLink.innerHTML = userjson.find((user) => {
                        return user.id == element.user_id;
                    }).name;
                    console.log(element.user_id);

                    /* Создаем блок времени отправки */
                    let authorTimeBlock = document.createElement("div");
                    authorTimeBlock.className = "row";
                    authorTimeBlock.innerHTML = element.send_date;

                    /* Добавляем ссылку в блок имени автора */
                    authorNameBlock.append(authorLink);

                    /* Добавляем блок имени и времени в блок автора */
                    authorBlock.append(authorNameBlock);
                    authorBlock.append(authorTimeBlock);

                    /* Создаем блок тела сообщения */
                    let messageBodyBlock = document.createElement("div");
                    messageBodyBlock.className = "col-11";
                    messageBodyBlock.id = "message-body";
                    messageBodyBlock.innerHTML = element.body_messages;

                    /* Добавляем блок автора и тела сообщения в блок сообщения */
                    messageBlock.append(authorBlock);
                    messageBlock.append(messageBodyBlock);

                    /* Добавляем блок сообщения в общий блок с сообщениями */
                    let messages = document.getElementById("chat-history");

                    messages.append(messageBlock);
                });
            }
        },
        error: function (data) {
            console.log(data);
        },
    });
}

/* Обработка изменения статуса готовности игрока */
function set_state_user_ready(
    user_id,
    csrf_token,
    is_ready,
    url_update_state_user
) {
    /**
     * Функция изменения статуса готовности игрока
     * user_id - id пользователя (авторизованной в системе)
     * csrf_token - токен для защиты от csrf атак
     * is_ready - статус готовности игрока
     */
    $.ajax({
        url: url_update_state_user,
        type: "POST",
        data: {
            _token: csrf_token,
            user_id: user_id,
            is_ready: is_ready,
        },
    });
}

/* Раз в 5 секунд опрашивать сервер на изменение статуса игрока в комнате */
function update_user_info(user_id, csrf_token, url_get_state_user) {
    /**
     * Функция обновления информации о пользователе
     * user_id - id пользователя (авторизованной в системе)
     * csrf_token - токен для защиты от csrf атак
     */

    $.ajax({
        url: url_get_state_user,
        type: "get",
        data: {
            _token: csrf_token,
            user_id: user_id,
        },
        success: function (data) {
            console.log("Пользователи и их статусы", data);
            /* Запрос успешен меняем статус всех пользователей  */
            user_in_room = data.user_in_room;
            user_in_room.forEach((element) => {
                let user = document.getElementById("userid-" + element.user_id);
                let is_ready = user.querySelector("#is_ready");
                if (element.is_ready) {
                    is_ready.innerHTML = ` Готовность:
                                            <span style="color:rgb(0, 255, 0)">
                                                Готов &nbsp;
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z" />
                                                    <path
                                                        d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z" />
                                                </svg>
                                            </span>
                                        `;
                } else {
                    is_ready.innerHTML = ` Готовность:
                                            <span style="color:rgb(217, 255, 0)">
                                                Не готов &nbsp;
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                                    <path class="colorChangeTimerSVG"
                                                        d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                                    <path class="colorChangeTimerSVG"
                                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                                </svg>
                                            </span>
                                        `;
                }
            });
        },
    });
}
