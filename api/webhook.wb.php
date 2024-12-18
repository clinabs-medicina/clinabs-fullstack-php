<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';
$data = file_get_contents('php://input');

$meet = json_decode($data);
$roomName = trim(str_replace('/', '', $meet->data->roomName));


if($meet->type == "room.client.joined") {
    if($meet->data->roleName == 'visitor') {
        $pdo->query("UPDATE `AGENDA_MED` SET `paciente_online` = 1 WHERE meet LIKE '%{$roomName}%' LIMIT 1;");
    } else {
        $pdo->query("UPDATE `AGENDA_MED` SET `medico_online` = 1 WHERE meet LIKE '%{$roomName}%' LIMIT 1;");
    }
}
else if($meet->type == "room.client.left") {
    if($meet->data->roleName == 'visitor') {
        $pdo->query("UPDATE `AGENDA_MED` SET `paciente_online` = 0 WHERE meet LIKE '%{$roomName}%' LIMIT 1;");
    } else {
        $pdo->query("UPDATE `AGENDA_MED` SET `medico_online` = 0 WHERE meet LIKE '%{$roomName}%' LIMIT 1;");
    }
}
