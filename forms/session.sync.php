<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';

if(isset($_GET['fetch'])) {
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $events = [];

    $tableName = $_GET['fetch'];

    $stmt = $pdo->query("SELECT token AS id,session_online,first_ping,last_ping,objeto FROM PACIENTES UNION( SELECT token AS id,session_online,first_ping,last_ping,objeto FROM MEDICOS ) UNION( SELECT token AS id,session_online,first_ping,last_ping,objeto FROM FUNCIONARIOS );");

    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

    $rws = [];

    foreach($rows as $row) {
        if((strtotime(date('Y-m-d H:i:s')) - strtotime($row->last_ping)) > 25) {
            $datetime = date('Y-m-d H:i:s');
            $token = $_GET['token'];

            $pdo->query("UPDATE {$row->objeto}S SET session_online = 0,last_ping = '{$datetime}' WHERE token = '{$row->id}'");
        }

        $start_time = new DateTime($row->first_ping);
        $end_time = new DateTime($row->last_ping);
        $diff = $end_time->diff($start_time);
        
        $hh =  $diff->format('%H');
        $mm = $diff->format('%I');
        $ss = $diff->format('%S');
        
        if(((int)$row->session_online) == 0) {
            $row->duration = "Offline";
        } else {
            if($hh > 0) {
                if($mm > 0) {
                    $row->duration = "{$hh} hora(s), {$mm} minuto(s)";
                }
            } else if($hh == 0 && $mm == 0) {
                $row->duration = "{$ss} segundo(s)";
            }
            else {
                $row->duration = "{$mm} minuto(s)";
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT);
} 
else {
    header('Cache-Control: no-store, no-cache, must-revalidate'); 
    $tableName = $_GET['user'];
    $token = $_GET['token'];

    $datetime = date('Y-m-d H:i:s');
    if (($tableName != 'none') && ($tableName != 'noneS') && ($tableName != 'clinabs_homolog.nones')) {
        $pdo->query("UPDATE {$tableName} SET session_online = 1,last_ping = '{$datetime}' WHERE token = '{$token}'");
    }
}