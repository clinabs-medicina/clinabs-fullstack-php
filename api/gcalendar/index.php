<?php
error_reporting(0);
ini_set('display_errors', 0);
date_default_timezone_set('America/Sao_Paulo');
$headers = ['Authorization' => 'all'];

$servername = 'localhost';
$database = 'clinabs_app';
$username = 'clinabs_admin';
$password = 'GenP+s+J6Cisa^vB7visr@%3c0nCaOz#3Bb7jaGJ6pyOqC*C';

date_default_timezone_set('America/Sao_Paulo');

// Banco de Dados
try {
	$pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Connection Failed: " . $e->getMessage();
}

require_once 'iCal.php';


function isTimeInRange($checkTime, $startTime, $endTime) {
    return strtotime($checkTime) >= strtotime($startTime) && strtotime($checkTime) <= strtotime($endTime);
}


    $stmt = $pdo->query("SELECT google_agenda_link,duracao_atendimento,token,(SELECT calendario FROM AGENDA_MEDICA WHERE medico_token = token) AS calendario FROM MEDICOS WHERE google_agenda_link != ''");

    if($stmt->rowCount() >= 1) {
        $medicos = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach($medicos as $medico) {
            $agendamentos = json_decode($medico->calendario, true);
            $excludes = [];
            $founded = [];


            $gCalendar= new GoogleCalendar($medico->google_agenda_link, $medico->duracao_atendimento);

            $eventsArray = [];

            foreach($gCalendar->parse() as $event) {
                if(isset($event['DTSTART'])) {
                    $inicio = date('H:i', strtotime($event['DTSTART']));
                    $fim = date('H:i', strtotime($event['DTEND']));
                    $times = $gCalendar->divideTimeIntoIntervals(date('H:i', strtotime($event['DTSTART'])), date('H:i', strtotime($event['DTEND'])), $medico->duracao_atendimento);

                    $eventsArray[] = [
                        'start_date' => date('Y-m-d H:i', strtotime($event['DTSTART'])),
                        'end_date' => date('Y-m-d H:i', strtotime($event['DTEND'])),
                        'duration' =>strtotime($event['DTEND']) - strtotime($event['DTSTART']),
                        'description' => $event['SUMMARY'],
                        'times' => $gCalendar->divideTimeIntoIntervals(date('H:i', strtotime($event['DTSTART'])), date('H:i', strtotime($event['DTEND'])), $medico->duracao_atendimento)
                    ];

                    $obj = date('Y-m-d', strtotime($event['DTSTART']));

                    foreach($agendamentos[$obj] as $key => $item)   {
                        if(strtotime($item['time']) >= strtotime($inicio) && strtotime($item['time']) <= strtotime($fim)) {
                            unset($agendamentos[$obj][$key]);
                        }
                    }
                }
            }

            try {
                
                $calendario = json_encode($agendamentos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                $pdo->query("UPDATE AGENDA_MEDICA SET calendario = '{$calendario}' WHERE medico_token = '{$medico->token}'");

                $eventsArray = [
                    'status' => 'success',
                    'text' => 'Agenda do Google Sincronizada com Sucesso!',
                    'icon' => 'success'
                ];
            } catch(Exception $ex) {
                $eventsArray = [
                    'status' => 'danger',
                    'text' => 'Falha ao Sincronizar Agenda do Google!',
                    'icon' => 'danger',
                    'exception' => $ex->getMessage()
                ];
            }
        }
    }

    
header('Content-Type: application/json; charset=utf-8');
echo json_encode($eventsArray, JSON_PRETTY_PRINT);
