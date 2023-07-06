@php
    $usersArray = json_decode($users, true);
    $usersNames = array_column($usersArray, 'name');
@endphp


<!DOCTYPE html>
<html lang="en">
@include('header')

<body>
    <div class="container-fluid">

        <div class="p-3">
            <div class="row">
                <div class="col-10">
                    <div class="col align-self-start">
                        <h2>Список комнат для игры</h2>
                    </div>
                </div>

                <div class="col-auto align-self-end ">
                    <button class="position-fixed ml-4 btn btn-success" data-toggle="modal" data-target="#exampleModal">
                        +Создать комнату
                    </button>
                </div>



                @error('access-denied-in-room')
                    <!-- Showing alert -->
                    <div id="alert" class="alert alert-danger">
                        {{ $message }}
                    </div>

                    <script type="text/javascript">
                        setTimeout(function() {

                            // Closing the alert
                            $('#alert').alert('close');
                        }, 5000);
                    </script>
                @enderror

                <!-- Modal для создания комнаты -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Создание комнаты для игры</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('rooms.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Название комнаты</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="Комната:{{ $lastRoomId + 1 }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Участники игры</label>
                                        <select multiple class="custom-select" name="users[]">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Отменить</button>
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        $('#exampleModal').on('shown.bs.modal', function() {
                            var lastRoomId = {{ $lastRoomId }};
                            var nextRoomId = lastRoomId + 1;
                            $('#name').val('Комната:' + nextRoomId);
                        });
                    });
                </script>
            </div>

            <div class="table-responsive  table-hover">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Имя комнаты</th>
                            <th>Участники</th>
                            <th>Дата создания</th>
                            <th>

                            </th>
                        </tr>
                    </thead>
                    {{-- Использую id для того что бы повесить обработчик на таблицу --}}
                    <tbody id="rooms-list">
                        @foreach ($rooms as $room)
                            {{-- На строки вешаем обработчик нажатие --}}
                            {{-- По клику заходим в комнату --}}
                            <tr>
                                {{-- Комнату берем по id --}}
                                <td>{{ $room->id }}</td>
                                <td>{{ $room->name }}</td>
                                <td>
                                    @foreach ($room->users as $user)
                                        {{ $user->name }},
                                    @endforeach
                                </td>
                                <td>{{ $room->create_data }}</td>
                                {{-- Только создатель может редактировать и удалять --}}
                                @php
                                    $games_can_edit_array = json_decode($games_can_edit, true);
                                    $games_array = json_decode($games, true);
                                @endphp

                                @if (in_array($room->id, array_column($games_can_edit_array, 'room_id')))
                                    <td>
                                        <button class="btn btn-warning edit-room-btn" data-toggle="modal"
                                            data-target="#editRoomModal" data-room-id="{{ $room->id }}"
                                            data-room-name="{{ $room->name }}">Изменить</button>

                                        <form action="{{ route('rooms.destroy', ['id' => $room->id]) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Удалить</button>
                                        </form>
                                    </td>
                                @else
                                    {{-- Проверяем, есть ли создатель комнаты --}}
                                    @if (in_array($room->id, array_column($games_array, 'room_id')))
                                        @php
                                            $creatorIndex = array_search($room->id, array_column($games_array, 'room_id'));
                                            $creatorId = $games_array[$creatorIndex]['creator_id'];
                                            $creatorName = isset($usersNames[$creatorId - 1]) ? $usersNames[$creatorId - 1] : '';
                                        @endphp
                                        <td>Создатель комнаты: {{ $creatorName }}</td>
                                    @endif
                                @endif
                            </tr>
                        @endforeach

                    </tbody>

                    <!-- Модальное окно для редактирования комнаты -->
                    <div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog"
                        aria-labelledby="editRoomModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editRoomModalLabel">Изменить комнату</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="editRoomForm" action="" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="roomName">Название комнаты</label>
                                            <input type="text" class="form-control" id="roomName" name="name">
                                        </div>
                                        <div class="form-group">
                                            <label for="roomUsers">Участники игры</label>
                                            <select multiple class="custom-select" id="roomUsers" name="users[]"
                                                size="5">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Отмена</button>
                                        <button type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function() {
                            $('.edit-room-btn').click(function() {
                                var roomId = $(this).data('room-id');
                                var roomName = $(this).data('room-name');
                                $('#editRoomModalLabel').text('Изменить комнату: ' + roomName);
                                $('#roomName').val(roomName);
                                $('#editRoomForm').attr('action', '/rooms/' + roomId);
                                $('#editRoomModal').modal('show');
                            });
                        });

                        // По двойному клику на строку таблицы переходим в комнату



                        $('#rooms-list tr').dblclick(function() {
                            // Получаем id комнаты
                            const roomId = $(this).children('td:first').text();
                            // Переходим в комнату
                            // url: /rooms/room/{id}
                            window.location.href = '/rooms/room/' + roomId;
                        });
                    </script>


                </table>
            </div>
        </div>
    </div>
</body>

</html>
