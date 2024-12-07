<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');

$roomName = $_GET['roomName'];

$stmt = $pdo->query("SELECT data_agendamento,duracao_agendamento,token,meet,medico_token,paciente_token FROM AGENDA_MED WHERE meet LIKE '%/{$roomName}%'");

if($stmt->rowCount() > 0) {
    $obj = $stmt->fetch(PDO::FETCH_OBJ);

    $meet = json_decode($obj->meet);

    if($user->tipo == 'MEDICO') {
        if($obj->medico_token == $user->token) {
            header('Location: /agenda/prescricao/'.$obj->token);
        } else {
            $msg ="Somente o Médico responsável por esta consulta pode acessar o link de Telemedicina";
        }
    } else if($user->tipo == 'PACIENTE') {
        if($obj->paciente_token == $user->token) {
            $startDate = date('Y-m-d H:i:s', strtotime($obj->data_agendamento) - 600);
            $endDate = date('Y-m-d H:i:s', strtotime($obj->data_agendamento) + ($obj->duracao_agendamento * 60) + 600);

            if(strtotime(date('Y-m-d H:i:s') >= (strtotime($obj->data_agendamento) - 600) && strtotime(date('Y-m-d H:i:s') < (strtotime($obj->data_agendamento) + ($obj->duracao_agendamento * 60) + 600)))) {
                header('Location: /agenda/prescricao/'.$obj->roomUrl);
            } else {
                $msg ="Horário Não Permitido para Acessar a Telemedicina para esta Consulta.";
            }
            
        } else {
            $msg ="Somente o Paciente pode acessar este link de Telemedicina";
        }
    } else {
        $msg ="Somente o Paciente ou Médico da consulta pode acessar este link de Telemedicina";
    }
} else {
    header('Location: /');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Restrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #e63946, #1d3557);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #ffffff;
            margin: 0rem 2rem;
        }
        .container {
            background: #ffffff;
            color: #333333;
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }
        .icon {
            font-size: 3em;
            color: #e63946;
            margin-bottom: 15px;
        }
        h1 {
            font-size: 1.8em;
            margin-bottom: 10px;
        }
        p {
            font-size: 1em;
            margin: 10px 0;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            color: #ffffff;
            background: #e63946;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        a:hover {
            background: #d62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h1>Acesso Restrito</h1>
        <p><?=$msg?></p>
        <p>Se você acredita que esta mensagem é um erro, entre em contato com o suporte.</p>
        <a href="https://wa.me/554133000790?text=Preciso de ajuda para acessar o link de telemedicina https://clinabs.com/teleconsulta/<?=$roomName?>">Fale com o suporte</a>
    </div>
</body>
</html>
