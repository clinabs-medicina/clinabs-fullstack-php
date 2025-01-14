<?php
require_once "../config.inc.php";

error_reporting(1);
ini_set('display_errors', true);


require_once "../class/agenda.class.php";


$stmt = $pdo->prepare("SELECT (SELECT calendario FROM AGENDA_MEDICA WHERE medico_token = token) AS calendario,google_agenda_link,duracao_atendimento,inicio_ag,fim_ag FROM MEDICOS WHERE token = :tk");
$stmt->bindValue(':tk', $_GET['token']);

$stmt->execute();

$item = $stmt->fetch(PDO::FETCH_OBJ);

$agenda = json_decode($item->calendario, true);

$ag = new AgendaGoogle(
  icsFile: $item->google_agenda_link,
);



$events = [];


$xstmt = $pdo->query("SELECT data_agendamento,modalidade FROM `AGENDA_MED` WHERE medico_token = '{$_GET['token']}';");

$agendamentos = $xstmt->fetchAll(PDO::FETCH_ASSOC);

foreach($ag->getEvents() as $date => $hour) {
  foreach($hour as $h => $hs) {
    if(strtotime($h) >= strtotime($item->inicio_ag) && strtotime($h) <= strtotime($item->fim_ag)) {
      $events[$date][$h] = $hs;
    }
  }
}



foreach($agendamentos as $x) {
  $date = date('Y-m-d', strtotime($x['data_agendamento']));
  $h =  date('H:i', strtotime($x['data_agendamento']));

  $events[$date][$h] = [
    'date' => $date,
    'time' => $h,
    'online' => strtolower($x['modalidade']) == 'online',
    'presencial' => strtolower($x['modalidade']) == 'presencial'
  ];
}




header('Content-Type: application/json; charset=utf-8');
if($_GET['view']) {
  echo json_encode($events, JSON_PRETTY_PRINT);
} else {
  if(is_array($events)) {
    $stmt2 = $pdo->prepare('UPDATE AGENDA_MEDICA SET calendario = :calendar WHERE medico_token = :tk');
    $stmt2->bindValue(':calendar', json_encode($events));
    $stmt2->bindValue(':tk', $_GET['token']);
  
    try {
      $stmt2->execute();
      $json = ['status' => 'success', 'icon' => 'success', 'text' => 'Calendário Sincronizado com Sucesso!'];
    } catch(Exception $ex) {
      $json = ['status' => 'success', 'icon' => 'success', 'text' => 'Erro ao Sincronizar Calendário'];
    }
  } else {
    $json = ['status' => 'success', 'icon' => 'success', 'text' => 'Erro ao Sincronizar Calendário'];
  }

  
  echo json_encode($json, JSON_PRETTY_PRINT);
}