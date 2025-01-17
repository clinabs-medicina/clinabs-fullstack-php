<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './googleCalendar.php';
$eventsArray = [];

if (isset($_REQUEST['token'])) {
  $condicao = "token = '{$_REQUEST['token']}'";
} else {
  $condicao = "google_agenda_link LIKE 'https://calendar.google.com/calendar/ical/%'";
}

date_default_timezone_set('America/Sao_Paulo');

$headers = ['Authorization' => 'all'];

$servername = '64.225.0.218';
$database = 'clinabs_homolog';
$username = 'clinabs_dev';
$password = '&?7z?Yw$0]62N!gbn=l_@bbA0O{TRg:s';

date_default_timezone_set('America/Sao_Paulo');

$removed = [];
$results = [];
// Banco de Dados
try {
  $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection Failed: ' . $e->getMessage();
}

function isTimeInRange($checkTime, $startTime, $endTime)
{
  return strtotime($checkTime) >= strtotime($startTime) && strtotime($checkTime) <= strtotime($endTime);
}

$stmt = $pdo->query("SELECT inicio_ag,fim_ag,google_agenda_link,duracao_atendimento,token,(SELECT calendario FROM AGENDA_MEDICA WHERE medico_token = token) AS calendario FROM MEDICOS WHERE {$condicao}");

if ($stmt->rowCount() >= 1) {
  $medicos = $stmt->fetchAll(PDO::FETCH_OBJ);

  foreach ($medicos as $medico) {
    $agendamentos = json_decode($medico->calendario, true);
    $excludes = [];

    $gCalendar = new GoogleCalendarSync();

    $gCalendar->setLink($medico->google_agenda_link, $medico->inicio_ag, $medico->fim_ag, $medico->duracao_atendimento);
    $gCalendar->fetch();
    $events = $gCalendar->parse();
    $events = $gCalendar->getEvents();

    $dates = [];

    foreach ($events as $event) {
      foreach ($event['DAYS'] as $day => $hours) {
        if (date('H:i', strtotime($event['DTSTART'])) != '00:00' && date('H:i', strtotime($event['DTEND'])) != '00:00') {
          $dates[date('Y-m-d', strtotime($event['DTSTART']))] = [
            'start' => date('H:i', strtotime($event['DTSTART'])),
            'end' => date('H:i', strtotime($event['DTEND']))
          ];
        }
      }
    }

    foreach ($agendamentos as $date => $item) {
      if (isset($dates[$date])) {
        foreach ($item as $h => $hrs) {
          $startTime = date('Y-m-d H:i', strtotime($event['DTSTART']));
          $endTime = date('Y-m-d H:i', strtotime($event['DTEND']));
          $checkTime = $date . ' ' . $h;

          $check = new DateTime($checkTime);
          $start = new DateTime($startTime);
          $end = new DateTime($endTime);

          if ($check >= $start && $check <= $end) {
            unset($agendamentos[$date][$h]);
          }
        }
      }
    }

    try {
      $calendario = json_encode($agendamentos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
      $pdo->query("UPDATE AGENDA_MEDICA SET calendario = '{$calendario}' WHERE medico_token = '{$medico->token}'");
    } catch (Exception $ex) {
      $eventsArray = [
        'status' => 'danger',
        'text' => 'Falha ao Sincronizar Agenda do Google!',
        'icon' => 'danger',
        'exception' => $ex->getMessage()
      ];
    }

    $results[] = $agendamentos;
  }
} else {
  $eventsArray = [
    'status' => 'danger',
    'text' => 'Nenhum médico encontrado!',
    'icon' => 'danger'
  ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($eventsArray, JSON_PRETTY_PRINT);
