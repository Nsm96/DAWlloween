<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('RUTA_USUARIOS_JSON', __DIR__ . '/usuarios.json');

function cargarUsuariosJSON() {
    if (!file_exists(RUTA_USUARIOS_JSON)) {
        return [];
    }
    $contenido = file_get_contents(RUTA_USUARIOS_JSON);
    return json_decode($contenido, true) ?? [];
}

function guardarUsuariosJSON($matrizUsuarios) {
    $json = json_encode($matrizUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(RUTA_USUARIOS_JSON, $json) !== false;
}

$_SESSION['mimatriz'] = cargarUsuariosJSON();
?>