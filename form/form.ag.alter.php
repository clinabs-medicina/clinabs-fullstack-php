<?php
require_once '../config.inc.php';
error_reporting(1);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['userObj'])) {
    $user = (object) $_SESSION['userObj'];
}

$data = $_REQUEST;
$dt1 = $data['data_agendamento'];
$hora = $data['hora_agendamento'];
$yyyy = trim(explode('/', $dt1)[2]);
$mm = trim(explode('/', $dt1)[1]);
$dd = trim(explode('/', $dt1)[0]);


$data['data_agendamento'] = "$yyyy-$mm-$dd $hora";
$code = $data['code'];
$xdata = $data;
$desc = $data['description'];
unset($data['code']);

if(isset($_REQUEST['alter_ag'])) 
{
    $data['status'] = 'PENDENTE';

    try {
        $xdata = json_encode($data, JSON_PRETTY_PRINT);
        $stmt = $pdo->prepare("UPDATE `AGENDA_MED` SET `data_alteracoes` = :json_data WHERE `AGENDA_MED`.`token` = :code");
        $stmt->bindValue(':json_data', $xdata);
        $stmt->bindValue(':code', $code);
        $stmt->execute();
    
        $res = ['status' => 'success'];
        $mid = $data['medico_id'];
    
        $ag = $pdo->query("SELECT data_agendamento,(SELECT nome_completo FROM MEDICOS WHERE id = '$mid') AS medico_nome2,(SELECT celular FROM MEDICOS WHERE id = '$mid') AS medico_celular2,(SELECT identidade_genero FROM MEDICOS WHERE id = '$mid') AS sexo2,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT celular FROM MEDICOS WHERE token = medico_token) as medico_celular,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome,(SELECT celular FROM PACIENTES WHERE token = paciente_token) as paciente_celular FROM `AGENDA_MED` WHERE token = '{$code}'");
        $ag = $ag->fetch(PDO::FETCH_OBJ);
    
        $dt2 =  date('d/m/Y H:i', strtotime($ag->data_agendamento));
        $prefixo = (strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr':'Dra.');
        $prefixo2 = (strtoupper($ag->sexo2) == 'MASCULINO' ? 'Dr':'Dra.');

        
        $msg2 = "Olá *{$prefixo} {$ag->medico_nome}*".PHP_EOL." a Consulta em: *{$dt2}* com o Paciente *{$ag->paciente_nome}* será alterado para *{$dt1} {$hora}* com *{$prefixo2} {$ag->medico_nome2}*.".PHP_EOL."*Justificativa:* $desc";
        $msg3 = "Olá *{$prefixo2} {$ag->medico_nome2}*".PHP_EOL." Podemos encaixar uma Consulta no dia: *{$dt1}* com o Paciente *{$ag->paciente_nome}* ás *{$hora}* em Sua Agenda? Aguardamos sua Aprovação.";

        $cel2 = preg_replace("/[^0-9]/", "", $ag->medico_celular);
        $cel3 = preg_replace("/[^0-9]/", "", $ag->medico_celular2);
        

        //$wa->sendLinkMessage($cel2, $msg2, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');
       // $wa->sendLinkMessage($cel3, $msg3, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');

        $res = ['status' => 'success', 'text' => 'Solicitação de Alteração foi Enviada com Sucesso , Aguarde a Confirmação'];
    } catch(Exception $error) {
        $res = ['status' => 'error', 'text' => $error->getMessage()];
    }
}
 else if (isset($_REQUEST['confirm_ag'])) 
 {
    $data['status'] = $_REQUEST['status'];
    $mid = $data['medico_id'];


    $alter = $pdo->query("SELECT token,valor_consulta,valor_consulta_online FROM MEDICOS WHERE id = '{$mid}';");
    $medico = $alter->fetch(PDO::FETCH_ASSOC);

    if($_REQUEST['status'] == 'CONFIRMADO') {
        $stmt = $pdo->query("SELECT * FROM `AGENDA_MED` WHERE `token` = '{$code}'");
        $ag = $stmt->fetch(PDO::FETCH_OBJ);
        $alt = json_decode($ag->data_alteracoes);
        $vl = number_format(preg_replace('/[^0-9]/', '', $alt->valor_restante ?? 0), 2, ',', '.');

        if($alt->acao == 'cobrar') {
            
            $addition = ", `status` = 'AGUARDANDO PAGAMENTO'"; 
            $stmt1 = $pdo->query("SELECT payment_id,celular,nome_completo FROM `PACIENTES` WHERE `token` = '{$ag->paciente_token}'");
            $ag1 = $stmt1->fetch(PDO::FETCH_OBJ);
            
            
            $payment = $asaas->cobrarCliente($ag1->payment_id, $ag->token, preg_replace('/[^0-9]/', '', $alt->valor_restante), 'AGENDAMENTO DE CONSULTA', $_REQUEST['payment_method'] ?? 'PIX');
            $DATA_AG = date('d/m/Y H:i', strtotime($ag->data_agendamento));
            
            $msgX = "Olá *{$ag1->nome_completo}*".PHP_EOL." sua Consulta em: *{$DATA_AG}* foi reagendada.".PHP_EOL."para efetivação  você precisa realizar o pagamento no valor de *R$ {$vl}*".PHP_EOL.".";

            $wa->sendLinkMessage($ag1->celular, $msgX, $payment->invoiceUrl, 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://'.$hostname.'/assets/images/logo.png');
        } else if($alt->acao == 'devolver') {
            $addition = ", `status` = 'AGENDADO'"; 
            $stmt1 = $pdo->query("SELECT payment_id,celular,nome_completo FROM `PACIENTES` WHERE `token` = '{$ag->paciente_token}'");
            $ag1 = $stmt1->fetch(PDO::FETCH_OBJ);
            $DATA_AG = date('d/m/Y H:i', strtotime($ag->data_agendamento));
            $msgX = "Olá *{$ag1->nome_completo}*".PHP_EOL." sua Consulta em: *{$DATA_AG}* foi reagendada.".PHP_EOL." Você vai receber um PIX no valor de *R$ {$vl}* referente a alteração de agendamento.".PHP_EOL.".";
            $wa->sendLinkMessage($ag1->celular, $msgX, $payment->invoiceUrl, 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://'.$hostname.'/assets/images/logo.png');
        } else {
            $addition = ", `status` = 'AGENDADO'"; 
            $stmt1 = $pdo->query("SELECT payment_id,celular,nome_completo FROM `PACIENTES` WHERE `token` = '{$ag->paciente_token}'");
            $ag1 = $stmt1->fetch(PDO::FETCH_OBJ);
            $DATA_AG = date('d/m/Y H:i', strtotime($ag->data_agendamento));
            $msgX = "Olá *{$ag1->nome_completo}*".PHP_EOL." sua Consulta em: *{$DATA_AG}* foi reagendada.";
            $wa->sendLinkMessage($ag1->celular, $msgX, $payment->invoiceUrl, 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://'.$hostname.'/assets/images/logo.png');
        }

        $dts = $_REQUEST;
        $dts['user_token'] = $user->token;
        $dts['user_nome'] = $user->nome_completo;
        
        $stmt = $pdo->prepare("UPDATE `AGENDA_MED` SET `valor` = :valor, `data_agendamento` = :dta,medico_token = :mt,data_alteracoes = :alt, last_alt = :last_alt, modalidade = :modalidade {$addition} WHERE `AGENDA_MED`.`token` = :code");
        $stmt->bindValue(':dta', $xdata['data_agendamento']);
        $stmt->bindValue(':code', $code);
        $stmt->bindValue(':modalidade', $_REQUEST['modalidade']);
        $stmt->bindValue(':mt', $medico['token']);
        $stmt->bindValue(':alt', '[]');
        $stmt->bindValue(':last_alt', json_encode($dts, JSON_PRETTY_PRINT));
        $stmt->bindValue(':valor', $_REQUEST['modalidade'] == 'PRESENCIAL' ? $medico['valor_consulta']:$medico['valor_consulta_online']);
        

        try 
        {
            $stmt->execute();

        $ag = $pdo->query("SELECT data_agendamento,(SELECT nome_completo FROM MEDICOS WHERE id = '$mid') AS medico_nome2,(SELECT celular FROM MEDICOS WHERE id = '$mid') AS medico_celular2,(SELECT identidade_genero FROM MEDICOS WHERE id = '$mid') AS sexo2,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT celular FROM MEDICOS WHERE token = medico_token) as medico_celular,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome,(SELECT celular FROM PACIENTES WHERE token = paciente_token) as paciente_celular FROM `AGENDA_MED` WHERE token = '{$code}'");
        $ag = $ag->fetch(PDO::FETCH_OBJ);
    
    
        $cel1 = preg_replace("/[^0-9]/", "", $ag->paciente_celular);
        $cel4 = preg_replace("/[^0-9]/", "", $ag->medico_celular2);
    
        $dt2 =  date('d/m/Y H:i', strtotime($ag->data_agendamento));
        $prefixo = (strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.':'Dra.');
        $prefixo2 = (strtoupper($ag->sexo2) == 'MASCULINO' ? 'Dr.':'Dra.');
    
        $msg1 = "Olá *{$ag->paciente_nome}*".PHP_EOL."Sua Consulta em: *{$dt2}* foi alterada para *{$dt1}*".PHP_EOL."*Justificativa:* $desc";
        $msg4 = "Olá *{$prefixo2} {$ag->medico_nome2}*".PHP_EOL." Foi feito o encaixe de uma Consulta em: *{$dt1}* com o Paciente *{$ag->paciente_nome}* em Sua Agenda.";

       //$wa->sendLinkMessage($cel1, $msg1, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');
        //$wa->sendLinkMessage($cel4, $msg4, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');

        $res = ['status' => 'success', 'text' => 'Alteração Confirmada com Sucesso!'];
        } catch(Exception $ex) {
            $res = ['status' => 'error', 'text' => 'Erro ao Alterar !'];
        }
    } else {
        $stmt = $pdo->prepare("UPDATE `AGENDA_MED` SET `data_alteracoes` = :json_data,medico_token = :mt WHERE `AGENDA_MED`.`token` = :code");
        $stmt->bindValue(':json_data', '[]');
        $stmt->bindValue(':mt', $_POST['medico_token']);
        $stmt->bindValue(':code', $code);

        try {
            $stmt->execute();

            $ag = $pdo->query("SELECT data_agendamento,(SELECT nome_completo FROM MEDICOS WHERE id = '$mid') AS medico_nome2,(SELECT celular FROM MEDICOS WHERE id = '$mid') AS medico_celular2,(SELECT identidade_genero FROM MEDICOS WHERE id = '$mid') AS sexo2,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT celular FROM MEDICOS WHERE token = medico_token) as medico_celular,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome,(SELECT celular FROM PACIENTES WHERE token = paciente_token) as paciente_celular FROM `AGENDA_MED` WHERE token = '{$code}'");
            $ag = $ag->fetch(PDO::FETCH_OBJ);
        
            $cel1 = preg_replace("/[^0-9]/", "", $ag->paciente_celular);
            $cel4 = preg_replace("/[^0-9]/", "", $ag->medico_celular2);
        
            $dt2 =  date('d/m/Y H:i', strtotime($ag->data_agendamento));
            $prefixo = (strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.':'Dra.');
            $prefixo2 = (strtoupper($ag->sexo2) == 'MASCULINO' ? 'Dr.':'Dra.');
        
            $msg1 = "Olá *{$ag->paciente_nome}*".PHP_EOL."Sua Consulta em: *{$dt2}* não foi alterada.".PHP_EOL."*Justificativa:* $desc";
            $msg4 = "Olá *{$prefixo2} {$ag->medico_nome2}*".PHP_EOL." não Foi feito o encaixe de do Paciente *{$ag->paciente_nome}* em Sua Agenda para o dia: *{$dt1}*";
        
            //$wa->sendLinkMessage($cel1, $msg1, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');
            //$wa->sendLinkMessage($cel4, $msg4, 'https://www.clinabs.com/', 'Agendamento de Consulta', 'Alteração de Agendamento', 'https://clinabs.com/assets/images/logo.png');
            $res = ['status' => 'success', 'text' => 'Alteração Salva com Sucesso!'];
        } catch(Exception $error) {
            $res = ['status' => 'error', 'text' => 'Ocorreu um Erro.'];
        }
    }
}


header('Content-Type: application/json');
echo json_encode($res, JSON_PRETTY_PRINT);