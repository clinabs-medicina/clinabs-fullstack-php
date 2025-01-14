<?php
require_once '../config.inc.php';
$token = $_REQUEST['medico_token'];
$date = $_REQUEST['data'];
$modalidade = strtolower($_REQUEST['modalidade']);

$dados = $pdo->query("SELECT calendario FROM `AGENDA_MEDICA` WHERE medico_token = '{$token}';");
$ags = $pdo->query("SELECT data_agendamento FROM `AGENDA_MED` WHERE medico_token = '{$token}' AND data_agendamento LIKE '{$date}%';")->fetchAll(PDO::FETCH_ASSOC);

$horarios = [];

$items = [];

if ($dados->rowCount() > 0) {
    $calendario = json_decode($dados->fetch(PDO::FETCH_ASSOC)['calendario'], true);

    foreach ($calendario[$date] as $obj) {
        if (isset($_REQUEST['only_time'])) {
            if ($obj[$modalidade] == true) {
                $add = true;

                foreach ($ags as $a) {
                    if (date('Y-m-d H:i', strtotime($a['data_agendamento'])) == date('Y-m-d H:i', strtotime("{$date} {$obj['time']}"))) {
                        $add = false;
                        break;
                    }
                }

                if ($add) {
                    if (strtotime("{$date} {$obj['time']}") >= strtotime(date('Y-m-d H:i'))) {
                        $items[] = $obj['time'];
                    }
                }
            }
        } else {
            if ($obj[$modalidade] == true) {
                $add = true;

                foreach ($ags as $a) {
                    if ($a['data_agendamento'] == "{$date} {$obj['time']}") {
                        $add = false;
                        break;
                    }
                }

                if ($add) {
                    if (strtotime("{$date} {$obj['time']}") >= strtotime(date('Y-m-d H:i'))) {
                        $items[] = $obj;
                    }
                }
            }
        }
    }

    $horarios = [
        'status' => 'success',
        'items' => $items,
        'agendados' => $ags,
        'queryString' => "SELECT data_agendamento FROM `AGENDA_MED` WHERE medico_token = '{$token}' AND data_agendamento LIKE '{$date}%';"
    ];
} else {
    $horarios = [
        'status' => 'error',
        'text' => 'Não Encontrado',
        'request' => $_REQUEST,
    ];
}

if (!isset($horarios['items'])) {
    $horarios['items'] = [];
}

header('Content-Type: application/json');
echo json_encode($horarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
