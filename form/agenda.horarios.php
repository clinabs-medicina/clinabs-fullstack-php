<?php
require_once '../config.inc.php';
$token = $_GET['medico_token'];
$date = $_GET['date'];

$dados = $pdo->query("SELECT calendario FROM `AGENDA_MEDICA` WHERE medico_token = '{$token}';");

if($dados->rowCount() > 0) {
    $calendario = json_decode($dados->fetch(PDO::FETCH_ASSOC)['calendario'], true);

    $ag = $calendario[$date];
} else {
    $ag = ['status' => 'error'];
}


header('Content-Type: application/json');
echo json_encode($ag, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);