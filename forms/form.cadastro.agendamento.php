<?php
global $agenda;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$ag = new stdClass();
$ag->token =  uniqid();

$ag->paciente_token = $_REQUEST['nome'];
$ag->medico_token = $_REQUEST['nome_medico'];
$ag->anamnese = $_REQUEST['anamnese'];
$ag->modalidade = $_REQUEST['atendimento'];
$ag->data_agendamento = trim($_REQUEST['data_agendamento']);
$ag->duracao_agendamento = $_REQUEST['duracao_agendamento'] ?? 30;
$ag->descricao = strtoupper($_REQUEST['descricao']);

$meet = new WhereByMeet();
$room = $meet->createRoom($_REQUEST['nome_medico']);
    
$ag->meet = json_encode($room);

if($agenda->Add($ag)) {
    
    $json = json_encode([
        'status' => 'success', 
        'text' => 'Agendamento Realizado Com Sucesso!'
    ], JSON_PRETTY_PRINT);

    $wa->sendLinkMessage(
        phoneNumber: $paciente->celular, 
        text: 'Agendamento de Consulta', 
        linkUrl: 'https://'.$hostname.'/api/pdf/?token='.$ag->token, 
        linkTitle: 'CLINABS', 
        linkDescription: sprintf("Sua Consulta %s com o DR(a) %s foi agendada para o dia %s as %s", $ag->modalidade, $medicos->getByToken($ag->medico_token)['nome_completo'], date('d/M/Y', $ag->data_agendamento), date('H:m', $ag->data_agendamento)), 
        linkImage: 'https://'.$hostname.'/assets/images/logo.svg'
    );
}else {
    $json = json_encode([
        'status' => 'error', 
        'text' => $agenda->lastException->getMessage()
    ], JSON_PRETTY_PRINT);
}

header('content-type: application/json');
echo $json;