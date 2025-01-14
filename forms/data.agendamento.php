<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
$hrs = [];
$agendados = [];

$ags = $pdo->query("SELECT data_agendamento FROM `AGENDA_MED` WHERE medico_token = '4365tfefdsf'")->fetchAll(PDO::FETCH_ASSOC);

$dados = $pdo->query("SELECT * FROM `AGENDA_MEDICA` WHERE medico_token = '4365tfefdsf'")->fetch(PDO::FETCH_ASSOC);

$calendario = json_decode($dados['calendario'], true);

foreach ($ags as $ag) {
    $agendados[date('Y-m-d', strtotime($ag['data_agendamento']))][] = date('H:i', strtotime($ag['data_agendamento']));
}

foreach($calendario[$_REQUEST['date']] ?? [] as $item) {
    $hrs[$item] = in_array($item, $agendados[$_REQUEST['date']]);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($hrs, 15|64|128);