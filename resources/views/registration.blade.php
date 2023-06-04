<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('header')

<body class="antialiased">
    <div class="container text-center MainWidget">
        <h1 class="align-self-center">Страницы регистрации</h1>
        <div class="">
            <form class="bg-primary col-3 offset-4 border rounded" method="POST"
                action="{{ route('user.registration') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="col-form-label-lg">Email</label>
                    <input class="form-control" id="email" name="email" type="text" value=""
                        placeholder="email" />
                    @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="col-form-label-lg">name</label>
                    <input class="form-control" id="name" name="name" type="text" value=""
                        placeholder="name" />
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="col-form-label-lg">password</label>
                    <input class="form-control" id="password" name="password" type="text" value=""
                        placeholder="password" />
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button class="btn btn-lg btn-primary" type="submit" name="sendMe" value="1">Войти</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
