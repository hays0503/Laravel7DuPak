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
<script type="module" src="{{ asset('js/app.js') }}"></script>
{{-- <script type="module" src="{{ asset('js/gamedice.js') }}"></script> --}}
<script type="module" >

    import * as CANNON from "https://cdn.skypack.dev/cannon-es";

    import * as THREE from "three";
    import * as BufferGeometryUtils from "three/addons/utils/BufferGeometryUtils.js";
    import {
        CSS3DRenderer,
        CSS3DObject,
    } from "three/addons/renderers/CSS3DRenderer.js";

    const canvasEl = document.querySelector("#canvas");
    const scoreResult = document.querySelector("#score-result");
    const rollBtn = document.querySelector("#roll-btn");


    let webGLRenderer, css3DRenderer, scene, camera, diceMesh, physicsWorld;
    let TableResultText, HelpMsg;
    let tableBody; // Глобальная переменная для хранения ссылки на тело таблицы
    //Сюда будем записывать результаты реальные имена людей из комнаты
    
    let JsonResult = [
        { id: 1, name: "Иван", score: 0 },
        { id: 2, name: "Петр", score: 0 },
        { id: 3, name: "Сергей", score: 0 },
    ];
    let UserIterator = 0;
    let UserSeed = 0;
    let UserStep = false;
    let UserAlterStep = false;

    const params = {
        numberOfDice: 2,
        segments: 40,
        edgeRadius: 0.07,
        notchRadius: 0.12,
        notchDepth: 0.1,
    };

    const diceArray = [];

    window.addEventListener("resize", updateSceneSize);

    rollBtn.addEventListener("click", () => {
                        console.log("dblclick");
                        sendSeed(window.location.href + "/UpdateCurrentUserAction", UserSeed);
                        initPhysics();
                        initScene();
                        throwDice(UserSeed);
                        UserStep = true;
                    });



    export function updateScene(url) {
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (data) {
                console.log("updateScene", data);
                if (data == null) return;

                if (data.current_user == data.current_user_action_id) {
                    // rollBtn.hide = false;
                    UserSeed = data.seed;
                    //Перезагрузка страницы
                    if (UserStep) {
                        if(UserAlterStep){
                            UserAlterStep = false;
                            location.reload();
                        }                            
                    }
                    
                } else {
                    // rollBtn.disabled = true;
                    // rollBtn.addEventListener("click",() => {
                    //     alert("Сейчас не ваш ход!");
                    // });
                    UserAlterStep = true
                    
                }
            },
        });
    }

    function sendSeed(url, seed) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: '{{csrf_token()}}',
                id_game: {{$id}},
                seed: seed,
            },
            success: function (data) {
                console.log("sendSeed", data);
            },
        });
    }



    setInterval(() => {
        const url = window.location.href + "/GetCurrentUserAction";
        updateScene(url);
    }, 500);

    function createHelp() {
        let element = document.createElement("div");
        let h4 = document.createElement("h4");
        h4.style.width = "100%";
        h4.style.height = "100%";
        h4.style.border = "1px solid black";
        h4.style.color = "white";
        h4.style.fontFamily = "Arial, sans-serif";
        h4.style.textAlign = "center";

        h4.innerHTML = `Игра в костяшки <br> Набери 100 очков для победы!`;

        element.appendChild(h4);
        element.style.backgroundColor =
            "rgba(0,127,127," + (Math.random() * 0.5 + 0.25) + ")";
        let objectCSS = new CSS3DObject(element);
        objectCSS.scale.set(0.05, 0.05, 0.05);

        return objectCSS;
    }

    function createTableScore() {
        let element = document.createElement("div");
        let table = document.createElement("table");
        table.style.width = "100%";
        table.style.height = "100%";
        table.style.border = "1px solid black";
        table.style.color = "white";
        table.style.fontFamily = "Arial, sans-serif";
        table.style.textAlign = "center";

        table.innerHTML = `<tr>
            <th style="padding: 8px; background-color: #333;">id</th>
            <th style="padding: 8px; background-color: #333;">Имя</th>
            <th style="padding: 8px; background-color: #333;">Счет</th>
        </tr>`;

        // Создать ссылку на тело таблицы
        tableBody = table.createTBody();

        JsonResult.forEach((UserScore) => {
            let row = tableBody.insertRow();
            row.insertCell().textContent = UserScore.id;
            row.insertCell().textContent = UserScore.name;
            row.insertCell().textContent = UserScore.score;
        });

        element.appendChild(table);
        element.style.backgroundColor =
            "rgba(0,127,127," + (Math.random() * 0.5 + 0.25) + ")";
        let objectCSS = new CSS3DObject(element);
        objectCSS.scale.set(0.05, 0.05, 0.05);

        return objectCSS;
    }
    function initScene() {
        webGLRenderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: true,
            canvas: canvasEl,
        });
        webGLRenderer.shadowMap.enabled = true;
        webGLRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        css3DRenderer = new CSS3DRenderer();
        css3DRenderer.setSize(window.innerWidth, window.innerHeight);
        css3DRenderer.domElement.style.position = "absolute";
        css3DRenderer.domElement.style.top = "0";
        document.body.appendChild(css3DRenderer.domElement);

        scene = new THREE.Scene();

        camera = new THREE.PerspectiveCamera(
            45,
            window.innerWidth / window.innerHeight,
            0.1,
            300
        );
        camera.position.set(0, 7, 11).multiplyScalar(3);
        camera.lookAt(0, 0, 0);

        updateSceneSize();

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.1);
        scene.add(ambientLight);
        let topLight = new THREE.SpotLight(0xffaaff);

        topLight.position.set(0, 5, 0);
        topLight.castShadow = true;
        topLight.shadow.mapSize.width = 2048;
        topLight.shadow.mapSize.height = 2048;
        topLight.shadow.camera.near = 5;
        topLight.shadow.camera.far = 400;
        topLight.shadow.camera.fov = 30;
        scene.add(topLight);

        createFloor();
        diceMesh = createDiceMesh();
        for (let i = 0; i < params.numberOfDice; i++) {
            diceArray.push(createDice());
            addDiceEvents(diceArray[i]);
        }

        // throwDice();

        TableResultText = createTableScore();
        TableResultText.position.set(0, 1, -1);
        scene.add(TableResultText);

        HelpMsg = createHelp();
        HelpMsg.position.set(0, 3, -5);
        scene.add(HelpMsg);

        render();
    }

    function initPhysics() {
        physicsWorld = new CANNON.World({
            allowSleep: true,
            gravity: new CANNON.Vec3(0, -50, 0),
        });
        physicsWorld.defaultContactMaterial.restitution = 0.3;
    }

    function createFloor() {
        const tileSize = 5; // Размер плитки доски

        for (let i = 0; i < 8; i++) {
            for (let j = 0; j < 8; j++) {
                const isWhite = (i + j) % 2 === 0; // Определение цвета плитки (белая или черная)

                const floorTile = new THREE.Mesh(
                    new THREE.BoxGeometry(tileSize, 1, tileSize),
                    new THREE.MeshStandardMaterial({
                        color: isWhite ? 0xffffff : 0x000000,
                    })
                );
                floorTile.receiveShadow = true;

                const positionX = (i - 3.5) * tileSize;
                const positionZ = (j - 3.5) * tileSize;
                floorTile.position.set(positionX, -7, positionZ);

                scene.add(floorTile);

                const floorTileShape = new CANNON.Box(
                    new CANNON.Vec3(tileSize / 2, 0.5, tileSize / 2)
                );
                const floorTileBody = new CANNON.Body({
                    mass: 0,
                    shape: floorTileShape,
                    position: new CANNON.Vec3(positionX, -7, positionZ),
                });
                physicsWorld.addBody(floorTileBody);
            }
        }
    }

    function createDiceMesh() {
        const boxMaterialOuter = new THREE.MeshStandardMaterial({
            color: 0xeeeeee,
        });
        const boxMaterialInner = new THREE.MeshStandardMaterial({
            color: 0x000000,
            roughness: 0,
            metalness: 1,
            side: THREE.DoubleSide,
        });

        const diceMesh = new THREE.Group();
        const innerMesh = new THREE.Mesh(createInnerGeometry(), boxMaterialInner);
        const outerMesh = new THREE.Mesh(createBoxGeometry(), boxMaterialOuter);
        outerMesh.castShadow = true;
        diceMesh.add(innerMesh, outerMesh);

        return diceMesh;
    }

    function createDice() {
        const mesh = diceMesh.clone();
        scene.add(mesh);

        const body = new CANNON.Body({
            mass: 1,
            shape: new CANNON.Box(new CANNON.Vec3(0.5, 0.5, 0.5)),
            sleepTimeLimit: 0.1,
        });
        physicsWorld.addBody(body);

        return { mesh, body };
    }

    function createBoxGeometry() {
        let boxGeometry = new THREE.BoxGeometry(
            1,
            1,
            1,
            params.segments,
            params.segments,
            params.segments
        );

        const positionAttr = boxGeometry.attributes.position;
        const subCubeHalfSize = 0.5 - params.edgeRadius;

        for (let i = 0; i < positionAttr.count; i++) {
            let position = new THREE.Vector3().fromBufferAttribute(positionAttr, i);

            const subCube = new THREE.Vector3(
                Math.sign(position.x),
                Math.sign(position.y),
                Math.sign(position.z)
            ).multiplyScalar(subCubeHalfSize);
            const addition = new THREE.Vector3().subVectors(position, subCube);

            if (
                Math.abs(position.x) > subCubeHalfSize &&
                Math.abs(position.y) > subCubeHalfSize &&
                Math.abs(position.z) > subCubeHalfSize
            ) {
                addition.normalize().multiplyScalar(params.edgeRadius);
                position = subCube.add(addition);
            } else if (
                Math.abs(position.x) > subCubeHalfSize &&
                Math.abs(position.y) > subCubeHalfSize
            ) {
                addition.z = 0;
                addition.normalize().multiplyScalar(params.edgeRadius);
                position.x = subCube.x + addition.x;
                position.y = subCube.y + addition.y;
            } else if (
                Math.abs(position.x) > subCubeHalfSize &&
                Math.abs(position.z) > subCubeHalfSize
            ) {
                addition.y = 0;
                addition.normalize().multiplyScalar(params.edgeRadius);
                position.x = subCube.x + addition.x;
                position.z = subCube.z + addition.z;
            } else if (
                Math.abs(position.y) > subCubeHalfSize &&
                Math.abs(position.z) > subCubeHalfSize
            ) {
                addition.x = 0;
                addition.normalize().multiplyScalar(params.edgeRadius);
                position.y = subCube.y + addition.y;
                position.z = subCube.z + addition.z;
            }

            const notchWave = (v) => {
                v = (1 / params.notchRadius) * v;
                v = Math.PI * Math.max(-1, Math.min(1, v));
                return params.notchDepth * (Math.cos(v) + 1);
            };
            const notch = (pos) => notchWave(pos[0]) * notchWave(pos[1]);

            const offset = 0.23;

            if (position.y === 0.5) {
                position.y -= notch([position.x, position.z]);
            } else if (position.x === 0.5) {
                position.x -= notch([position.y + offset, position.z + offset]);
                position.x -= notch([position.y - offset, position.z - offset]);
            } else if (position.z === 0.5) {
                position.z -= notch([position.x - offset, position.y + offset]);
                position.z -= notch([position.x, position.y]);
                position.z -= notch([position.x + offset, position.y - offset]);
            } else if (position.z === -0.5) {
                position.z += notch([position.x + offset, position.y + offset]);
                position.z += notch([position.x + offset, position.y - offset]);
                position.z += notch([position.x - offset, position.y + offset]);
                position.z += notch([position.x - offset, position.y - offset]);
            } else if (position.x === -0.5) {
                position.x += notch([position.y + offset, position.z + offset]);
                position.x += notch([position.y + offset, position.z - offset]);
                position.x += notch([position.y, position.z]);
                position.x += notch([position.y - offset, position.z + offset]);
                position.x += notch([position.y - offset, position.z - offset]);
            } else if (position.y === -0.5) {
                position.y += notch([position.x + offset, position.z + offset]);
                position.y += notch([position.x + offset, position.z]);
                position.y += notch([position.x + offset, position.z - offset]);
                position.y += notch([position.x - offset, position.z + offset]);
                position.y += notch([position.x - offset, position.z]);
                position.y += notch([position.x - offset, position.z - offset]);
            }

            positionAttr.setXYZ(i, position.x, position.y, position.z);
        }

        boxGeometry.deleteAttribute("normal");
        boxGeometry.deleteAttribute("uv");
        boxGeometry = BufferGeometryUtils.mergeVertices(boxGeometry);

        boxGeometry.computeVertexNormals();

        return boxGeometry;
    }

    function createInnerGeometry() {
        const baseGeometry = new THREE.PlaneGeometry(
            1 - 2 * params.edgeRadius,
            1 - 2 * params.edgeRadius
        );
        const offset = 0.48;
        return BufferGeometryUtils.mergeBufferGeometries(
            [
                baseGeometry.clone().translate(0, 0, offset),
                baseGeometry.clone().translate(0, 0, -offset),
                baseGeometry
                    .clone()
                    .rotateX(0.5 * Math.PI)
                    .translate(0, -offset, 0),
                baseGeometry
                    .clone()
                    .rotateX(0.5 * Math.PI)
                    .translate(0, offset, 0),
                baseGeometry
                    .clone()
                    .rotateY(0.5 * Math.PI)
                    .translate(-offset, 0, 0),
                baseGeometry
                    .clone()
                    .rotateY(0.5 * Math.PI)
                    .translate(offset, 0, 0),
            ],
            false
        );
    }

    function addDiceEvents(dice) {
        dice.body.addEventListener("sleep", (e) => {
            dice.body.allowSleep = false;

            const euler = new CANNON.Vec3();
            e.target.quaternion.toEuler(euler);

            const eps = 0.1;
            let isZero = (angle) => Math.abs(angle) < eps;
            let isHalfPi = (angle) => Math.abs(angle - 0.5 * Math.PI) < eps;
            let isMinusHalfPi = (angle) => Math.abs(0.5 * Math.PI + angle) < eps;
            let isPiOrMinusPi = (angle) =>
                Math.abs(Math.PI - angle) < eps || Math.abs(Math.PI + angle) < eps;

            if (isZero(euler.z)) {
                if (isZero(euler.x)) {
                    showRollResults(1);
                } else if (isHalfPi(euler.x)) {
                    showRollResults(4);
                } else if (isMinusHalfPi(euler.x)) {
                    showRollResults(3);
                } else if (isPiOrMinusPi(euler.x)) {
                    showRollResults(6);
                } else {
                    // landed on edge => wait to fall on side and fire the event again
                    dice.body.allowSleep = true;
                }
            } else if (isHalfPi(euler.z)) {
                showRollResults(2);
            } else if (isMinusHalfPi(euler.z)) {
                showRollResults(5);
            } else {
                // landed on edge => wait to fall on side and fire the event again
                dice.body.allowSleep = true;
            }
        });
    }

    function showRollResults(score) {
        if (scoreResult.innerHTML === "") {
            scoreResult.innerHTML += score;
        } else {
            const score_all = parseInt(scoreResult.innerHTML) + score;

            scoreResult.innerHTML = score_all;
            JsonResult[UserIterator].score += score_all;
            if (score_all >= 12) {
                scoreResult.innerHTML = "Вы получаете немного фансервиса! =)";
                // Показать кнопку
                document.querySelector("#show-secrets").style.display = "block";
            }
            // Переключить пользователя
            if (UserIterator < JsonResult.length - 1) {
                UserIterator++;
            } else {
                UserIterator = 0;
            }
        }

        // Обновить все данные
        let index = 0;
        JsonResult.forEach((row) => {
            tableBody.rows[index].cells[2].textContent = row.score;
            index++;
        });
    }

    function render() {
        physicsWorld.fixedStep();

        for (const dice of diceArray) {
            dice.mesh.position.copy(dice.body.position);
            dice.mesh.quaternion.copy(dice.body.quaternion);
        }

        const dice1Position = diceArray[0].body.position.clone();
        const dice2Position = diceArray[1].body.position.clone();

        const centerPositionDice = new THREE.Vector3()
            .addVectors(dice1Position, dice2Position)
            .multiplyScalar(0.5);

        const distanceDice = dice1Position.distanceTo(dice2Position);
        const cameraOffsetDice = new THREE.Vector3(0, distanceDice, distanceDice);

        // camera.position.copy(centerPositionDice).add(cameraOffsetDice);
        camera.position.set(0, 5, 7).multiplyScalar(4);
        camera.lookAt(centerPositionDice);

        webGLRenderer.render(scene, camera);
        css3DRenderer.render(scene, camera);

        requestAnimationFrame(render);
    }

    function updateSceneSize() {
        // Маштаб
        const scale = 0.8;
        camera.aspect = window.innerWidth / (window.innerHeight * scale);
        camera.updateProjectionMatrix();
        webGLRenderer.setSize(window.innerWidth, window.innerHeight * scale);
        css3DRenderer.setSize(
            window.innerWidth - 0.4,
            window.innerHeight * scale - 0.4
        );
    }
    function throwDice(RandomMatch) {
        scoreResult.innerHTML = "";

        diceArray.forEach((d, dIdx) => {
            d.body.velocity.setZero();
            d.body.angularVelocity.setZero();

            d.body.position = new CANNON.Vec3(6, dIdx * 1.5, 0);
            d.mesh.position.copy(d.body.position);

            // const MRandom1 = Math.random();
            // const MRandom2 = Math.random();
            // const MRandom3 = Math.random();

            const MRandom1 = +RandomMatch;
            const MRandom2 = MRandom1 + 0.01;
            const MRandom3 = MRandom2 + 0.01;

            console.log(
                "MRandom1=",
                MRandom1,
                "MRandom2=",
                MRandom2,
                "MRandom3=",
                MRandom3
            );

            d.mesh.rotation.set(2 * Math.PI * MRandom1, 0, 2 * Math.PI * MRandom2);
            d.body.quaternion.copy(d.mesh.quaternion);

            const force = 3 + 5 * MRandom3;
            d.body.applyImpulse(
                new CANNON.Vec3(-force, force, 0),
                new CANNON.Vec3(0, 0, 0.2)
            );

            d.body.allowSleep = true;
        });
    }
</script>
<script type="module">
        console.log('2')
        //Спрятать кнопку показать фан сервис
        document.querySelector("#show-secrets").style.display = "none";


        $('#show-secrets').click(() => {
            setTimeout(() => {
                window.location.href = "http://" + window.location.host + "/mmd/index.html";
            }, 5000);
        });
</script>


<body>
    {{-- Анимация борска костей а потом вывод информации кто выиграл --}}
    {{-- Анимация сделанна с помощью css  --}}
    {{-- Скрипт который выбирает кто выиграл использует Генератор случайных цифр --}}
    <main>
        <div class="content">
            <div class="ui-controls">
                <div class="score">На сколько ты удачен: <span id="score-result"></span></div>
                <button id="roll-btn"> Бросаем кости </button>
                <button id='show-secrets'>Показать фан сервис</button>
            </div>

            <canvas id="canvas"></canvas>

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
