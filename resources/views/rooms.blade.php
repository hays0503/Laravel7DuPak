@php
    $users = \App\User::all();
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


                <!-- Modal для создание комнаты -->
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
                                <form>
                                    <div class="form-group">
                                        <label for="name">Название комнаты</label>
                                        <input type="name" class="form-control" id="name"
                                            placeholder="Комната:1">
                                    </div>
                                    <div class="form-group">
                                        <label>Участники игры</label>
                                        <select multiple class="custom-select">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                                <button type="button" class="btn btn-primary">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <tbody>
                        <tr>
                            <th>1</th>
                            <td>Комната 1</td>
                            <td>Участники 1,Участники 2, Участники 3</td>
                            <td>05.05.05</td>
                            <td>
                                <button class="btn btn-warning">Изменить</button>
                                <button class="btn btn-danger">Удалить</button>
                            </td>
                        </tr>
                        <tr>
                            <th>2</th>
                            <td>Комната 2</td>
                            <td>Участники 4,Участники 5, Участники 6</td>
                            <td>05.05.05</td>
                            <td>
                                <button class="btn btn-warning">Изменить</button>
                                <button class="btn btn-danger">Удалить</button>
                            </td>
                        </tr>
                        <tr>
                            <th>3</th>
                            <td>Комната 3</td>
                            <td>Участники 7,Участники 8, Участники 9</td>
                            <td>05.05.05</td>
                            <td>
                                <button class="btn btn-warning">Изменить</button>
                                <button class="btn btn-danger">Удалить</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
