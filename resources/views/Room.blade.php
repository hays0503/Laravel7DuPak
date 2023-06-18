<!DOCTYPE html>
<html lang="en">

@include('header')


<style>
    .wrapper {
        position: relative;
    }

    .gold-box {
        position: absolute;
        z-index: 3;
        /* помещаем .gold-box поверх .green-box и .dashed-box */
        width: 100%;
        /* opacity: 0.95; */
    }

    .green-box {
        position: absolute;
        z-index: 2;
        /* помещаем .green-box поверх .dashed-box */
        right: 50%;
        opacity: 0.5;
    }
</style>

<body class="body-h100">
    <div class="custom-container-fluid">

        <div class="wrapper">
            <div class="green-box">
                @include('dice')
            </div>
            <div class="gold-box">
                @include('Chat')
            </div>
        </div>
    </div>
</body>

</html>
