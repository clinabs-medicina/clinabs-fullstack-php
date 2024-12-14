<?php
global $agenda;
ini_set('display_errors', 1);
error_reporting(1);
use PHPMailer\PHPMailer\PHPMailer;
require_once '../config.inc.php';

function get_ref_status($sts,$btn, $pdo, $paciente, $wap, $token) {
    $result = $sts;
    $action = $btn;
    try {
    error_log("Valor da variável agenda \$action: $action\r\n" . PHP_EOL);
    } catch (PDOException $e) {
    }

    switch($btn) {
        case 'agenda-accept': {
            $result = "AGENDADO";
            $action = "agenda-accept";

            $meet = new WhereByMeet();
            $room = $meet->createRoom(uniqid());

                $stmt2 = $pdo->prepare('UPDATE AGENDA_MED SET `status` = :sts, `meet` = :meet WHERE `token` = :token');
                $stmt2->bindValue(':sts', 'AGENDADO');
                $stmt2->bindValue(':token', $token);
                $stmt2->bindValue(':meet', json_encode($room));
                
                try{
                    $stmt2->execute();

                    $stmt = $pdo->prepare('SELECT 
                    data_agendamento,
                    token,
                    valor AS valor_consulta,
                    status,
                    (SELECT MEDICOS.nome_completo 
                    FROM MEDICOS 
                    WHERE MEDICOS.token = AGENDA_MED.medico_token)  AS medico_nome,
                    (SELECT PACIENTES.nome_completo 
                    FROM PACIENTES 
                    WHERE PACIENTES.token = AGENDA_MED.paciente_token)  AS paciente_nome,
                    (SELECT PACIENTES.celular 
                    FROM PACIENTES 
                    WHERE PACIENTES.token = AGENDA_MED.paciente_token)  AS celular,
                    (SELECT PACIENTES.email 
                    FROM PACIENTES 
                    WHERE PACIENTES.token = AGENDA_MED.paciente_token)  AS email
                    FROM AGENDA_MED WHERE `token` = :token');
                    $stmt->bindValue(':token', $token);

                    $stmt->execute();


                    $item = $stmt->fetch(PDO::FETCH_OBJ);

                    $date = date('d/m/Y', strtotime($item->data_agendamento));
                    $time = date('H:i', strtotime($item->data_agendamento));
                    if(isset($payment)) {
                        $valor = $payment->amount;
                    } else {
                        $valor = 0;
                    }
                    $medico_nome = $item->medico_nome;
                    $paciente_nome = $item->paciente_nome;
                    $celular = $item->celular;
                    

                    $wap->sendLinkMessage(
                        phoneNumber: $celular, 
                        text: "Olá {$paciente_nome}, Sua Consulta foi Agendada para o dia ".date('d/m/Y', strtotime($item->data_agendamento))." as ".date('H:i', strtotime($item->data_agendamento))."\n com DR(a) {$medico_nome}, clique no link no dia para acessar a Teleconsulta.", 
                        linkUrl: $room->roomUrl, 
                        linkTitle: 'CLINABS', 
                        linkDescription: 'Financeiro', 
                        linkImage: 'https://clinabs.com/assets/images/logo.png'
                    );  


                    // Email
                    $mailer = new PHPMailer();
                    $mailer->IsSMTP();
                    $mailer->SMTPDebug = false;
                    $mailer->SMTPSecure = 'ssl';
                    $mailer->Port = 465; //Indica a porta de conexão
                    $mailer->Host = 'smtplw.com.br';//Endereço do Host do SMTP
                    $mailer->SMTPAuth = true; //define se haverá ou no autenticação
                    $mailer->Username = 'suporteuno'; //Login de autenticação do SMTP
                    $mailer->Password = 'Acesso23Pr@'; //Senha de autenticação do SMTP
                    $mailer->FromName = 'CLINABS'; //Nome que será exibido
                    $mailer->From = 'naoresponder@clinabs.com'; //Obrigatório ser
                    $mailer->isHTML(true);

                try{
                    $mailer->addAddress($item->email, '');

                    $date = date('d/m/Y', strtotime($item->data_agendamento));
                    $time = date('H:i', strtotime($item->data_agendamento));

                    $mailer->isHTML(true);
                    $mailer->Subject = 'Consulta Agendada';
                    $mailer->Body    =  utf8_decode("<img src=\"https://clinabs.com/assets/images/logo.svg\"><br><br>
                    Prezado(a) <b>{$paciente->nome_completo}</b>,<br><br>
                    Pagamento realizado com sucesso!<br><br>
                    Informamos que sua consulta, agendada para o dia {$date}, às {$time}h, est confirmada.<br>
                    <b>Valor da Consulta: R$ ".$item->valor_consulta."</b>.<br><br>
                    
                    Para acessar o sistema online de consultas, clique no link abaixo:<br>
                    <b>Acessar Telemedicina:</b> {$room->roomUrl}<br><br>
                    
                    No sistema, você poderá consultar o horário, o local e o médico da sua consulta. Você também poderá visualizar a sua ficha médica e realizar o check-in para a sua consulta.
                    Agradecemos a sua preferência.<br><br>
                    Atenciosamente,<br>
                    <b>Clinabs</b> - Telemedicina com especialistas em CBD: a medicina do futuro, acessível a todos.<br><br>
                    <b>Informações importantes:</b><br><br>
                    - Não é necessário imprimir o comprovante de agendamento.<br>
                    - Se você não puder comparecer à consulta, cancele-a com antecedência para que o horrio possa ser disponibilizado para outro paciente.<br>
                    - Se você tiver alguma dúvida, entre em contato conosco pelo telefone ou whatsapp:  (41) 3300-0341");


                    $mailer->send();
                } catch (Exception $e) {
                   // echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
                }

                $result = $item->status;
                $action = 'AGENDADO';

                }catch(Exception $ex) {
                    $result = $_REQUEST['status'];
                    $action = $_REQUEST['action'];
                }

                
            break;
        }
        case 'agenda-cancel': {
            $result = "CANCELADO";
            $action = "agenda-cancel";

            break;
        }
    }

    return [
        'status' => $result,
        'action' => $action
    ];
}

$paciente = $pacientes->getPacienteByToken($_REQUEST['paciente_token']);
$res = get_ref_status($_REQUEST['ref'], $_REQUEST['action'], $pdo, $pacientes, $wa, $_REQUEST['token']);

$ag = new stdClass();
$ag->token = $_REQUEST['token'];
$ag->status = $res['status'];

if($res['status'] != $_REQUEST['status']) {
    $json = json_encode([
        'status' => 'success',
        'text' => 'Agendamento Alterado com Sucesso!',
        'newStatus' => $ag->status,
        'ref' => $ag->status,
        'action' => $res['action']
    ], JSON_PRETTY_PRINT);
} else {
    $json = json_encode([
        'status' => 'error', 
        'text' => $agenda->lastException != false ? $agenda->lastException->getMessage() : 'Erro ao Atualizar',
        'request' => $_REQUEST,
        'response' => $ag->status
    ], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json');
echo $json;