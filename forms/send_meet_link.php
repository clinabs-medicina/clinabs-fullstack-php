<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once '../config.inc.php';

$text = 'clique no link para acessar a sala de Tele-Consulta';

$stmtz = $pdo->prepare("SELECT
                            data_agendamento,
                            data_cancelamento,
                        	(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,
                        	(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome,
                        	(SELECT celular FROM PACIENTES WHERE token = paciente_token) AS paciente_celular,
                        	(SELECT celular FROM MEDICOS WHERE token = medico_token) AS medico_celular,
                        	(SELECT email FROM PACIENTES WHERE token = paciente_token) AS paciente_email, 
	                        (SELECT email FROM MEDICOS WHERE token = medico_token) AS medico_email,
	                        (SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,
                        	medico_token, 
                        	token, 
                        	meet
                        FROM
                        	AGENDA_MED
                        WHERE
                        	token = :token");
$stmtz->bindValue(':token', $_GET['token']);

$stmtz->execute();

$dado = $stmtz->fetch(PDO::FETCH_OBJ);

$meet = json_decode($dado->meet);
$sexo = $dado->sexo == 'Masculino' ? 'DR.':'DRA.';

$data = date('d/m/Y', strtotime($dado->data_agendamento == '0000-00-00 00:00:00' ? $dado->data_cancelamento : $dado->data_agendamento));
$horario = date('H:i', strtotime($dado->data_agendamento == '0000-00-00 00:00:00' ? $dado->data_cancelamento : $dado->data_agendamento));


try {
    $result = $wa->sendLinkMessage($dado->medico_celular, "Olá {$dado->medico_nome},
    segue o Link para a Tele-Consulta com o Paciente {$dado->paciente_nome}
    em {$data} às {$horario}hs", $meet->hostRoomUrl,
    'TeleConsulta',
    'Link de Acesso', 
    'https://'.$hostname.'/data/images/profiles/'.$dado->medico_token.'.jpg');
}catch(Exception $ex) {
    
}

try {
    $result = $wa->sendLinkMessage($dado->paciente_celular, "Olá {$dado->paciente_nome},
    segue o Link para a sua Tele-Consulta com {$sexo} {$dado->medico_nome}
    em {$data} às {$horario}hs", $meet->roomUrl, 
    'TeleConsulta', 
    'Link de Acesso', 
    'https://'.$hostname.'/data/images/profiles/'.$dado->medico_token.'.jpg');
}catch(Exception $ex) {
    
}


// Email
                    $mailer = new PHPMailer();
                    $mailer->IsSMTP();
                    $mailer->SMTPDebug = false;
                    $mailer->SMTPSecure = 'ssl';
                    $mailer->Port = 465; //Indica a porta de conexão
                    $mailer->Host = 'smtplw.com.br';//Endereço do Host do SMTP
                    $mailer->SMTPAuth = true; //define se haverá ou não autenticação
                    $mailer->Username = 'suporteuno'; //Login de autenticação do SMTP
                    $mailer->Password = 'Acesso23Pr@'; //Senha de autenticação do SMTP
                    $mailer->FromName = 'CLINABS'; //Nome que será exibido
                    $mailer->From = 'naoresponder@clinabs.com'; //Obrigatório ser
                    $mailer->isHTML(true);

                try{
                    $mailer->addAddress($dado->paciente_email, $dado->paciente_nome);
                    $mailer->addAddress('adrianoneco673@gmail.com');

                    $mailer->isHTML(true);
                    $mailer->Subject = 'Consulta Agendada';
                    $mailer->Body    =  utf8_decode("<img src=\"https://$hostname/assets/images/logo.svg\"><br><br>
                    Prezado(a) <b>{$dado->nome_completo}</b>,<br><br>
                    Olá {$dado->paciente_nome},
                    segue o Link para a sua Tele-Consulta com {$sexo} {$dado->medico_nome}
                    em {$agendamento} as {$horario}hs
                    <hr>
                    <a href=\"{$meet->roomUrl}\">{$meet->roomUrl}</a>
                    <hr>
                    No sistema, você poderá consultar o horário, o local e o médico da sua consulta. Você também poderá visualizar a sua ficha médica e realizar o check-in para a sua consulta.
                    Agradecemos a sua preferência.<br><br>
                    Atenciosamente,<br>
                    <b>Clinabs</b> - Telemedicina com especialistas em CBD: a medicina do futuro, acessível a todos.<br><br>
                    <b>Informações importantes:</b><br><br>
                    - Não é necessário imprimir o comprovante de agendamento.<br>
                    - Se você não puder comparecer à consulta, cancele-a com antecedência para que o horário possa ser disponibilizado para outro paciente.<br>
                    - Se você tiver alguma dúvida, entre em contato conosco pelo telefone ou whatsapp:  (41) 3300-0341");


                    $mailer->send();
                } catch (Exception $e) {
 
                }
                
                
                
                 try{
                    $mailer->addAddress($dado->medico_email, $dado->medico_nome);
                    $mailer->addAddress('adrianoneco673@gmail.com');

                    $mailer->isHTML(true);
                    $mailer->Subject = 'Consulta Agendada';
                    $mailer->Body    =  utf8_decode("<img src=\"https://$hostname/assets/images/logo.svg\"><br><br>
                    Prezado(a) <b>{$dado->nome_completo}</b>,<br><br>
                    Olá {$dado->medico_nome},
                    segue o Link para a Tele-Consulta com o Paciente {$dado->paciente_nome}
                    em {$data} as {$horario}hs
                    <hr>
                    <a href=\"{$meet->hostRoomUrl}\">{$meet->hostRoomUrl}</a>
                    <hr>
                    No sistema, você poderá consultar o horário, o local e o médico da sua consulta. Você também poderá visualizar a sua ficha médica e realizar o check-in para a sua consulta.
                    Agradecemos a sua preferência.<br><br>
                    Atenciosamente,<br>
                    <b>Clinabs</b> - Telemedicina com especialistas em CBD: a medicina do futuro, acessível a todos.<br><br>
                    <b>Informações importantes:</b><br><br>
                    - Não é necessário imprimir o comprovante de agendamento.<br>
                    - Se você não puder comparecer à consulta, cancele-a com antecedência para que o horário possa ser disponibilizado para outro paciente.<br>
                    - Se você tiver alguma dúvida, entre em contato conosco pelo telefone ou whatsapp:  (41) 3300-0341");


                    $mailer->send();
                    
                    
                    
                } catch (Exception $e) {
              
                }


header('Content-Type: application/json');
echo json_encode(
    [
        'icon' => 'success',
        'text' => 'Link Enviado',
        'width' => 'auto',
        'heightAuto' => true,
        'timer' => 3000,
        'timerProgressBar' => true,
        'showCancelButton' => false,
        'showConfirmButton' => false,
        'allowOutsideClick' => false
    ], JSON_PRETTY_PRINT);
    
