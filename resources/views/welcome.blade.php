<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

@include('header')


<body class="body-h100">
    <div class="container-fluid">
        <div class="bg-dark">

            <div class="bg-secondary bgdogs">
                <div class="roledimg">
                    @foreach (range(1, 14) as $imgindex)
                        <img id="leftmove" src='{{ asset("/media/dogs/$imgindex.png") }}' />
                    @endforeach
                </div>
                <div class="roledimg">
                    @foreach (range(11, 24) as $imgindex)
                        <img id="rightmove" src='{{ asset("/media/dogs/$imgindex.png") }}' />
                    @endforeach
                </div>
                <div class="roledimg">
                    @foreach (range(21, 34) as $imgindex)
                        <img id="leftmove" src='{{ asset("/media/dogs/$imgindex.png") }}' />
                    @endforeach
                </div>
                <div class="roledimg">
                    @foreach (range(11, 24) as $imgindex)
                        <img id="rightmove" src='{{ asset("/media/dogs/$imgindex.png") }}' />
                    @endforeach
                </div>

            </div>
            <div class="contentblock text-center align-items-center">
                <h1>Приветствуем вас, дорогие игроки, на нашем сайте онлайн игры в дурака!</h1>
                <p class="h5">
                    <em>
                        Мы рады приветствовать вас в нашем виртуальном игровом мире, где вас ждут увлекательные партии и
                        незабываемые моменты.

                        Желаем вам отличного времени в нашей игре! Пусть каждая карта, которую вы разыгрываете, приносит
                        вам
                        радость и удовлетворение. Пусть ваша стратегия будет проницательной, а удача всегда на вашей
                        стороне.
                        Играйте с умом, а также с наслаждением, ведь в этом заключается суть настоящего игрового опыта.

                        Помните, что наша игровая платформа предназначена для объединения игроков со всего мира.
                        Встречайте
                        новых друзей, обменивайтесь тактиками и учитеся друг у друга. У нас вы найдете сообщество
                        единомышленников, готовых сразиться в увлекательных битвах интеллекта.

                        Будьте вежливы и уважайте других игроков. Помните, что дружеская атмосфера создает самые
                        запоминающиеся
                        впечатления. Независимо от результата каждой партии, помните, что главное - это радость от игры
                        и
                        возможность расслабиться в приятной компании.

                        <blockquote class="blockquote  text-right">
                            <p class="mb-0"> Пожелаем вам удачи, увлекательных партий и незабываемого игрового опыта!
                                Наслаждайтесь игрой в
                                дурака и
                                окунитесь в захватывающий мир карт. Будем рады видеть вас снова на нашем сайте!</p>
                            <footer class="blockquote-footer ">С уважением,
                                <cite title="Source Title">Команда онлайн игры в дурака</cite>
                            </footer>
                        </blockquote>
                    </em>
                </p>

                <a class='button-green' href="{{ route('rooms') }}">Приступить к игре</a>


            </div>
        </div>

    </div>
</body>

</html>
