<?php
require_once '../config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/libs/sendMail.php';

ini_set('display_errors', 1);
error_reporting(1);

$action = $_REQUEST['action'];
$token = $_REQUEST['token'];

$stmt = $pdo->prepare('SELECT * FROM VENDAS WHERE reference = :token');
$stmt->bindValue(':token', $token);

$json = [];

$stmt->execute();
$venda = $stmt->fetch(PDO::FETCH_OBJ);

$json['request'] = $_REQUEST;
$json['response'] = $venda;

$stmtx = $pdo->prepare('SELECT
	VENDAS.*, 
	(SELECT PACIENTES.nome_completo FROM PACIENTES WHERE token = VENDAS.customer) AS nome_completo,
	(SELECT PACIENTES.celular FROM PACIENTES WHERE token = VENDAS.customer) AS celular,
	(SELECT PACIENTES.email FROM PACIENTES WHERE token = VENDAS.customer) AS email
FROM
	VENDAS
WHERE VENDAS.reference = :token');

$stmtx->bindValue(':token', $token);

try {
    $stmtx->execute();
} catch(PDOException $ex) {
    file_put_contents('erro.wa.txt', print_r($ex, true));
}
    
$transaction = $stmtx->fetch(PDO::FETCH_OBJ);
    
    
switch($action) {
    case 'payment-cancel': {
        $sts = 'CANCELADO';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

            $stmt = $pdo->query("UPDATE AGENDA_MED SET `data_cancelamento` = data_agendamento WHERE token = '{$token}'");
            $stmt = $pdo->prepare('UPDATE AGENDA_MED SET `data_agendamento` = "0000-00-00 00:00:00" WHERE token = :ref');
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

        
            $json = [
                'status' => 'success',
                'text' => 'Pedido Cancelado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
            
            $wa->sendLinkMessage(
                $transaction->celular, 
                'Olá *'.$transaction->nome_completo.'*
                Seu Pagamento no valor de R$ *'.number_format($transaction->amount, 2, ',', '.').'* referente a transação *'.$token.'* foi Cancelada.',
                '', 
                'CLINABS', 
                'Financeiro', 
                'https://clinabs.com/assets/images/logo.png'
            );
            
            
            sendMail(
                mailer: $mailer,
                to: array('email' => $transaction->email, 'name' => $transaction->nome_completo), 
                subject: 'Finaceiro', 
                body: 'Seu Pagamento no valor de R$ *'.number_format($transaction->amount, 2, ',', '.').'* referente a transação *'.$token.'* foi Cancelada.'
            );

        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Cancelar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'payment-undo': {
        $sts = $venda->paid == 1 ? 'PAGO':'AGUARDANDO PAGAMENTO';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

        
            $json = [
                'status' => 'success',
                'text' => 'Pedido Cancelado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
            
            
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Cancelar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'payment-accept': {
        $sts = 'PAGO';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts, paid = 1 WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

            $json = [
                'status' => 'success',
                'text' => 'Pedido Aterado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'newStatus' => 'PAGO',
                'result' => $sts
            ];
            
            $wa->sendLinkMessage(
                $transaction->celular, 
                'Olá *'.$transaction->nome_completo.'*
                Seu Pagamento no valor de R$ *'.number_format($transaction->amount, 2, ',', '.').'* referente a transação *'.$token.'* foi *PAGO* com sucesso.',
                '', 
                'CLINABS', 
                'Financeiro', 
                'https://clinabs.com/assets/images/logo.png'
            );
            
             sendMail(
                mailer: $mailer,
                to: array('email' => $transaction->email, 'name' => $transaction->nome_completo), 
                subject: 'Finaceiro', 
                body:  'Seu Pagamento no valor de R$ *'.number_format($transaction->amount, 2, ',', '.').'* referente a transação *'.$token.'* foi *PAGO* com sucesso.'
            );
            
            
            file_put_contents('teste.txt', print_r($transaction, true));

            if($transaction->module == 'AGENDA_MED' && $transaction->method != 'sc') {
                
            try {
                $stmt2 = $pdo->prepare("INSERT INTO `clinabs_db`.`CRONTAB` (`nome`, `data`, `message`,`celular`, `type`, `status`, `output`)
                VALUES (:nome, :data, :message, :celular, :type, :status, :output);");
                
                $ag = $pdo->prepare("SELECT
                                    	AGENDA_MED.*, 
                                    	(SELECT nome_completo FROM PACIENTES WHERE token = AGENDA_MED.paciente_token) AS paciente_nome,
                                    	(SELECT celular FROM PACIENTES WHERE token = AGENDA_MED.paciente_token) AS paciente_celular,
                                    	(SELECT nome_completo FROM MEDICOS WHERE token = AGENDA_MED.medico_token) AS medico_nome,
                                    	(SELECT celular FROM MEDICOS WHERE token = AGENDA_MED.medico_token) AS medico_celular
                                    FROM
                                    	AGENDA_MED 
                                    	WHERE token = :ref");
                                    	
                $ag->bindValue(':ref', $transaction->reference);
                $ag->execute();
                
                $pac = $ag->fetch(PDO::FETCH_OBJ);
            
                
                $paciente_nome = $pac->paciente_nome;
                $paciente_celular = $pac->paciente_celular;
                
                $medico_nome = $pac->medico_nome;
                $medico_celular = $pac->medico_celular;
                
                
                $data = date('Y-m-d H:i:s', strtotime($pac->data_agendamento));
                $data_exec = date('Y-m-d H:i:s', strtotime($pac->data_agendamento.' -1 hour'));
                $dt = date('H:i', strtotime($data));
                $link = json_decode($pac->meet)->roomUrl;
                
                $stmt2->bindValue(':nome', 'Agendamento');
                $stmt2->bindValue(':data', $data_exec);
                $stmt2->bindValue(':message', "Olá *{$paciente_nome}*
                VOCÊ TEM UMA CONSULTA AGENDADA HOJE AS *{$dt}* 
                COM DR(a). *{$medico_nome}*'
                
                clique no link abaixo para acessar a Teleconsulta!
                
                {$link}");
                
                $stmt2->bindValue(':celular', $pac->paciente_celular);
                $stmt2->bindValue(':type', 'consulta');
                $stmt2->bindValue(':status', 'PENDING');
                $stmt2->bindValue(':output', '');
                
                 $stmt2->execute();
                
                $wa->sendLinkMessage(
                $transaction->celular, 
                'Olá *'.$transaction->nome_completo.'*
                Sua Consulta para o Dia *'.date('d/m/Y', strtotime($data)).'* as *'.date('H:i', strtotime($data)).'* com DR(a) *'.$medico_nome.'* foi Agendada com Sucesso!',
                $link, 
                'CLINABS', 
                'Agendamento de Consultas', 
                'https://clinabs.com/assets/images/logo.png'
            );
            
            
            sendMail(
                mailer: $mailer,
                to: array('email' => $transaction->email, 'name' => $transaction->nome_completo), 
                subject: 'Agendamento de Consultas', 
                body:  'Sua Consulta para o Dia *'.date('d/m/Y', strtotime($data)).'* as *'.date('H:i', strtotime($data)).'* com DR(a) *'.$medico_nome.'* foi Agendada com Sucesso!'
            );
            
                
               
            } catch(PDOException $e){
                file_put_contents('erro.payment.txt', print_r($e, true));
            }
            }
            
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Alterar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
        }

        break;
    }
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);