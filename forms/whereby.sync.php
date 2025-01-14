<?php
require_once '../config.inc.php';
$no_debug = true;
$roomName = $_GET['roomName'];

$stmt = $pdo->query("SELECT medico_online,paciente_online FROM AGENDA_MED WHERE meet LIKE '%{$roomName}%' LIMIT 1;");
$res = $stmt->fetch(PDO::FETCH_OBJ);
$res->socket = "/{$roomName}";
$res->roomId = $_GET['roomId'];


header('Content-Type: application/json; charset=utf-8');
echo json_encode($res, JSON_PRETTY_PRINT);