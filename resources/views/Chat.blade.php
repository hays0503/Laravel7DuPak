@section('chat')
    {{-- Чат --}}
    <div class="col-10">
        <div class="chat">
            {{-- История сообщений загружаются с сервера посредством ajax запросов --}}
            <div class="flex-row" id="chat-history">
                {{-- Cмотри тэг скрипт --}}
            </div>
            {{-- Поле ввода сообщения --}}
            <div class="flex-row" id="chat-input">
                <div class="col-10">
                    <input type="text" class="form-control" id="message" placeholder="Сообщение">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-primary" id="send-message">Отправить</button>
                </div>
            </div>
        </div>
        <script>
            

        </script>
    @endsection
