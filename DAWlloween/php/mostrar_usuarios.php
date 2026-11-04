<?php
session_start();
header('Content-Type: application/json');


$usuario = $_SESSION['currentUser'] ?? null;

echo json_encode(['usuario' => $usuario]);
?>