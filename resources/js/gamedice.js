import * as CANNON from "https://cdn.skypack.dev/cannon-es";
import { set } from "lodash";

import * as THREE from "three";
import * as BufferGeometryUtils from "three/addons/utils/BufferGeometryUtils.js";

const canvasEl = document.querySelector("#canvas");
const scoreResult = document.querySelector("#score-result");
const rollBtn = document.querySelector("#roll-btn");

let renderer, scene, camera, diceMesh, physicsWorld;

const params = {
    numberOfDice: 2,
    segments: 40,
    edgeRadius: 0.07,
    notchRadius: 0.12,
    notchDepth: 0.1,
};

const diceArray = [];

initPhysics();
initScene();

window.addEventListener("resize", updateSceneSize);
window.addEventListener("dblclick", throwDice);
rollBtn.addEventListener("click", throwDice);

function initScene() {
    renderer = new THREE.WebGLRenderer({
        alpha: true,
        antialias: true,
        canvas: canvasEl,
    });
    renderer.shadowMap.enabled = true;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

    scene = new THREE.Scene();

    camera = new THREE.PerspectiveCamera(
        45,
        window.innerWidth / window.innerHeight,
        0.1,
        300
    );
    camera.position.set(0, 1, 0).multiplyScalar(1);
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

    throwDice();

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
        scoreResult.innerHTML +=
            "+" + score + "=" + (parseInt(scoreResult.innerHTML) + score);
        if (score_all >= 5) {
            scoreResult.innerHTML = "Вы получаете немного фансервиса! =)";
            setTimeout(() => {
                window.location.href = ENV.APP_URL + "/public/mmd/index.html";
            }, 5000);
        }
    }
}

function render() {
    physicsWorld.fixedStep();

    for (const dice of diceArray) {
        dice.mesh.position.copy(dice.body.position);
        dice.mesh.quaternion.copy(dice.body.quaternion);
    }

    const dice1Position = diceArray[0].body.position.clone();
    const dice2Position = diceArray[1].body.position.clone();

    const centerPosition = new THREE.Vector3()
        .addVectors(dice1Position, dice2Position)
        .multiplyScalar(0.5);

    const distance = dice1Position.distanceTo(dice2Position);
    const cameraOffset = new THREE.Vector3(0, distance, distance); // Смещение камеры относительно центра

    camera.position.copy(centerPosition).add(cameraOffset); // Установка позиции камеры
    camera.lookAt(centerPosition); // Взгляд на центр камеры

    renderer.render(scene, camera);
    requestAnimationFrame(render);
}

function updateSceneSize() {
    // Маштаб
    const scale = 0.8;
    camera.aspect = window.innerWidth / (window.innerHeight * scale);
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight * scale);
}

function throwDice() {
    scoreResult.innerHTML = "";

    diceArray.forEach((d, dIdx) => {
        d.body.velocity.setZero();
        d.body.angularVelocity.setZero();

        d.body.position = new CANNON.Vec3(6, dIdx * 1.5, 0);
        d.mesh.position.copy(d.body.position);

        d.mesh.rotation.set(
            2 * Math.PI * Math.random(),
            0,
            2 * Math.PI * Math.random()
        );
        d.body.quaternion.copy(d.mesh.quaternion);

        const force = 3 + 5 * Math.random();
        d.body.applyImpulse(
            new CANNON.Vec3(-force, force, 0),
            new CANNON.Vec3(0, 0, 0.2)
        );

        d.body.allowSleep = true;
    });
}
