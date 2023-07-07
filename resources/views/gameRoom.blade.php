{{-- Реализация комнаты для игры в дурака --}}
{{-- При Нажатия на таблицу комнаты будет --}}
{{-- открыта эта вьюшка тем самым мы обозначаем вход в комнату --}}

<!DOCTYPE html>
<html lang="en">
{{-- Импортивуем стандартную шапку с панелью польхователя(логин/регистрация) --}}
@include('header')
<link rel="stylesheet" type="text/css" src="{{ asset('css/base.css') }}" />
<script>
    document.documentElement.className = "js";
    var supportsCssVars = function() {
        var e, t = document.createElement("style");
        return t.innerHTML = "root: { --tmp-var: bold; }", document.head.appendChild(t), e = !!(window.CSS && window
                .CSS.supports && window.CSS.supports("font-weight", "var(--tmp-var)")), t.parentNode.removeChild(t),
            e
    };
    supportsCssVars() || alert("Please view this demo in a modern browser that supports CSS Variables.");
</script>
<script type="module" src="{{ asset('js/gamedice.js') }}" defer></script>

<body>
    {{-- Анимация борска костей а потом вывод информации кто выиграл --}}
    {{-- Анимация сделанна с помощью css  --}}
    {{-- Скрипт который выбирает кто выиграл использует Генератор случайных цифр --}}
    <main>
        <div class="content">
            <canvas id="canvas"></canvas>
            <div class="ui-controls">
                <div class="score">На сколько ты удачен: <span id="score-result"></span></div>
                <button id="roll-btn"> Бросаем кости </button>
            </div>
        </div>
    </main>
    <script async src="https://unpkg.com/es-module-shims@1.3.6/dist/es-module-shims.js"></script>
    <script type="importmap">
      {
        "imports": {
          "three": "https://unpkg.com/three@0.138.0/build/three.module.js",
          "three/addons/": "https://unpkg.com/three@0.138.0/examples/jsm/"
        }
      }
    </script>
</body>

</html>
