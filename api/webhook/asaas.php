<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';

error_reporting(1);
ini_set('display_errors', 1);

$data = json_decode(
    file_get_contents('php://input')
);

$event = $data->id;

try {
    $pdo->query('UPDATE VENDAS SET `asaas_payload` = "'.json_encode($data->payment).'" WHERE payment_id = "'.$data->payment->id.'"');
} catch(Exception $ex) {
    file_put_contents('erro.log', $ex->getMessage());
}

$payment_status = [
    'RECEIVED' => 'PAGO',
    'PENDING' => 'PENDENTE',
    'UNDEFINED' => 'NÃO DEFINIDO PELO CLIENTE',
    'BOLETO' => 'BOLETO BANCÁRIO',
    'RECEIVED_IN_CASH' => 'PAGO',
    'REFUND_REQUESTED' => 'ESTORNO SOLICITADO',
    'CREDIT_CARD' => 'CARTÃO DE CRÉDITO',
    'CONFIRMED' => 'PAGO'
];

if (isset($data->payment)) {
    $pdo->query("INSERT IGNORE INTO `PAYMENT_TYPES` (`name`) VALUES ('{$data->payment->billingType}');");
    $pdo->query("INSERT IGNORE INTO `PAYMENT_TYPES` (`name`) VALUES ('{$data->payment->status}');");
}

$user = $asaas->getCliente($data->payment->customer);

$stmtx = $pdo->query("SELECT *,(SELECT celular FROM PACIENTES WHERE token = paciente_token) AS paciente_celular,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT celular FROM MEDICOS WHERE token = medico_token) AS celular FROM AGENDA_MED WHERE token = '{$data->payment->externalReference}'");
$ag = $stmtx->fetch(PDO::FETCH_OBJ);

if ($data->event == 'PAYMENT_CONFIRMED' && $data->payment->billingType == 'CREDIT_CARD' || $data->payment->billingType == 'DEBIT_CARD' ) {
    $paymentId = $data->payment->id;
    $paymentType = $data->payment->billingType;
    $paymentStatus = $data->payment->status;

    $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
    $stmt->bindValue(':pm', $paymentType);
    $stmt->bindValue(':sts', ($paymentStatus == 'RECEIVED_IN_CASH' || $paymentStatus == 'RECEIVED' || $paymentStatus == 'CONFIRMED') ? 'AGENDADO' : $asaas->get_status($paymentStatus));
    $stmt->bindValue(':id', $paymentId);

    
    $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'O Dr.' : 'a Dra.';
    $dta = date('H:i', strtotime($ag->data_agendamento));

    try {
        $stmt->execute();
        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $ag->paciente_celular,
                'Olá *' . $user->name . '*' . PHP_EOL . 'O Pagamento referente a Consulta  com ' . $prefixo . ' ' . $ag->medico_nome . ' no dia ' . date('d/m/Y H:i', strtotime($ag->data_agendamento)) . ' no valor R$ ' . number_format($data->payment->value, 2, ',', '.') . ' foi confirmado com Sucesso!',
                'https://clinabs.com/',
                'Financeiro',
                $data->payment->description,
                'https://clinabs.com/assets/images/logo.png'
            );
        }

        if (strtotime($ag->data_agendamento) < time()) {
            $ag->data_agendamento = $ag->data_cencelamento;
        }

        $msg = 'Olá, o Paciente  *' . $user->name . '*' . PHP_EOL;
        $msg .= 'Realizou o  Pagamento no valor de R$ ' . number_format($data->payment->value, 2, ',', '.') . ' referente a transação *' . $data->payment->id . '* foi *PAGO* com sucesso.';
        $msg .= '' . PHP_EOL;
        $msg .= 'Data do Agendamento: ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));

        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $finaceiro_notificacao,
                $msg,
                '',
                $data->payment->description,
                ''
            );
        }

        // Paciente
        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'O Dr.' : 'a Dra.';
        $dta = date('H:i', strtotime($ag->data_agendamento));
        $date = date('d/m/Y', strtotime($ag->data_agendamento));

        $msg = "Olá *{$user->name}*" . PHP_EOL;
        $msg .= "Você tem uma Consulta Agendada com *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
        $msg .= "em {$date} às {$dta}";

        if ($ag->modalidade == 'ONLINE') {
            $meetlink = json_decode($ag->meet)->roomUrl;
        }

        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $ag->paciente_celular,
                $msg,
                '',
                $data->payment->description,
                ''
            );
        }

        

        try {
            $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
            $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - (10 * 60));
            $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$data->payment->externalReference}', '{$event}_1');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$data->payment->externalReference}''{$event}_2');");
        } catch (PDOException $ex) {
        
        }

        // Médico
        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'O Dr.' : 'a Dra.';
        $dta = date('H:i', strtotime($ag->data_agendamento));
        $date = date('d/m/Y', strtotime($ag->data_agendamento));

        $msg = "Olá *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
        $msg .= "Você tem uma Consulta Agendada com o Paciente *{$user->name}*" . PHP_EOL;
        $msg .= "em {$date} as {$dta}" . PHP_EOL;
        $msg .= "Contato: {$user->mobilePhone}" . PHP_EOL;

        if ($ag->modalidade == 'ONLINE') {
            $meetlink = json_decode($ag->meet)->hostRoomUrl;
        }

        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_2'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $ag->celular,
                $msg,
                '',
                $data->payment->description,
                ''
            );
        }

        try {
            $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
            $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 600);
            $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$data->payment->externalReference}', '{$event}_3');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$data->payment->externalReference}', '{$event}_4');");
        } catch (PDOException $ex) {
            
        }
    } 
    catch (Exception $ex) {
  
    }
}

else if ($data->event == 'PAYMENT_RECEIVED' && $data->payment->status == 'RECEIVED_IN_CASH') {
        $paymentId = $data->payment->id;
        $paymentType = $data->payment->billingType;
        $paymentStatus = $data->payment->status;

        $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
        $stmt->bindValue(':pm', $paymentType);
        $stmt->bindValue(':sts', 'AGENDADO');
        $stmt->bindValue(':id', $paymentId);


        try {
            $stmt->execute();
            $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';

            $meet = json_decode($ag->meet);

            if ($ag->modalidade == 'ONLINE') {
                $meetlink = json_decode($ag->meet)->roomUrl;
            } else {
                $meetlink = 'https://clinabs.com/agenda';
            }

            /*
            $wa->sendLinkMessage(
                $ag->paciente_celular,
                'Olá *' . $user->name . '*' . PHP_EOL . 'O Pagamento referente a Consulta  com ' . $prefixo . ' ' . $ag->medico_nome . ' no dia ' . date('d/m/Y H:i', strtotime($ag->data_agendamento)) . ' no valor R$ ' . number_format($data->payment->value, 2, ',', '.') . ' foi confirmado com Sucesso!',
                'https://clinabs.com',
                'Financeiro',
                $data->payment->description,
                'https://clinabs.com/assets/images/logo.png'
            );
            */
        
            $msg = 'Olá, o Paciente  *' . $user->name . '*' . PHP_EOL;
            $msg .= 'Realizou o  Pagamento no valor de R$ ' . number_format($data->payment->value, 2, ',', '.') . ' referente a transação *' . $data->payment->id . '* foi *PAGO* com sucesso.';
            $msg .= '' . PHP_EOL;
            $msg .= 'Data do Agendamento: ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));
            // fim
            /*
            $wa->sendLinkMessage(
                $finaceiro_notificacao,
                $msg,
                '',
                $data->payment->description,
                ''
            );
            */
            

            // Paciente

            $dta = date('H:i', strtotime($ag->data_agendamento));
            $date = date('d/m/Y', strtotime($ag->data_agendamento));

            $msg = "Olá *{$user->name}*" . PHP_EOL;
            $msg .= "Sua consulta foi confirmada com *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
            $msg .= "em *{$date}* as *{$dta}* no Horário de Brasília (GMT -3).";

            if($ag->modalidade == 'ONLINE') {
                $msg .= PHP_EOL."*Link da Teleconsulta:* {$meetlink}";

                $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

                if($xstmtx->rowCount() == 0) {
                        $wa->sendLinkMessage(
                            $ag->paciente_celular,
                            $msg,
                            'https://clinabs.com/',
                            'Financeiro',
                            $data->payment->description,
                            'https://clinabs.com'.Modules::user_get_image($ag->medico_token)
                        );
                    }
            }else {
                $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_2'");

                if($xstmtx->rowCount() == 0) {
                        $wa->sendLinkMessage(
                            $ag->paciente_celular,
                            $msg,
                            'https://clinabs.com/agenda',
                            'Financeiro',
                            $data->payment->description,
                            'https://clinabs.com'.Modules::user_get_image($ag->medico_token)
                        );
                    }
            }
            

            try {
                $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
                $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 600);
                $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_1');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_2');");
            } catch (PDOException $ex) {
        
            }

            // Médico
            $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';
            $dta = date('H:i', strtotime($ag->data_agendamento));
            $date = date('d/m/Y', strtotime($ag->data_agendamento));

            $msg = "Olá *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
            $msg .= "Você tem uma Consulta Agendada com o Paciente *{$user->name}*" . PHP_EOL;
            $msg .= "em {$date} as {$dta}" . PHP_EOL;
            $msg .= "Contato: {$user->mobilePhone}" . PHP_EOL;

            if ($ag->modalidade == 'ONLINE') {
                $meetlink = json_decode($ag->meet)->hostRoomUrl;
            }

            $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_2'");

            if($xstmtx->rowCount() == 0) {
                $wa->sendLinkMessage(
                    $ag->celular,
                    $msg,
                    '',
                    $data->payment->description,
                    ''
                );
            }
            

            $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);

            try {
                $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
                $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 600);
                $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_3');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_4');");
            } catch (PDOException $ex) {

            }
        } catch (Exception $ex) {

        }
} 

else if ($data->event == 'PAYMENT_RECEIVED' && $data->payment->status == 'RECEIVED' && $data->payment->billingType == 'PIX') {
    $paymentId = $data->payment->id;
    $paymentType = $data->payment->billingType;
    $paymentStatus = $data->payment->status;

    $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
    $stmt->bindValue(':pm', $paymentType);
    $stmt->bindValue(':sts', 'AGENDADO');
    $stmt->bindValue(':id', $paymentId);


    try {
        $stmt->execute();
        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';

        $meet = json_decode($ag->meet);

        if ($ag->modalidade == 'ONLINE') {
            $meetlink = json_decode($ag->meet)->roomUrl;
        } else {
            $meetlink = 'https://clinabs.com/agenda';
        }


        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $ag->paciente_celular,
                'Olá *' . $user->name . '*' . PHP_EOL . 'O Pagamento referente a Consulta  com ' . $prefixo . ' ' . $ag->medico_nome . ' no dia ' . date('d/m/Y H:i', strtotime($ag->data_agendamento)) . ' no valor R$ ' . number_format($data->payment->value, 2, ',', '.') . ' foi confirmado com Sucesso!',
                'https://clinabs.com',
                'Financeiro',
                $data->payment->description,
                'https://clinabs.com/assets/images/logo.png'
            );
        }
        
    
        $msg = 'Olá, o Paciente  *' . $user->name . '*' . PHP_EOL;
        $msg .= 'Realizou o  Pagamento no valor de R$ ' . number_format($data->payment->value, 2, ',', '.') . ' referente a transação *' . $data->payment->id . '* foi *PAGO* com sucesso.';
        $msg .= '' . PHP_EOL;
        $msg .= 'Data do Agendamento: ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));
        // fim

        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $finaceiro_notificacao,
                $msg,
                '',
                $data->payment->description,
                ''
            );
        }

        

        // Paciente

        $dta = date('H:i', strtotime($ag->data_agendamento));
        $date = date('d/m/Y', strtotime($ag->data_agendamento));

        $msg = "Olá *{$user->name}*" . PHP_EOL;
        $msg .= "Sua consulta foi confirmada com *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
        $msg .= "em *{$date}* as *{$dta}* no Horário de Brasília (GMT -3).";

        if($ag->modalidade == 'ONLINE') {
            $msg .= PHP_EOL."*Link da Teleconsulta:* {$meetlink}";

            $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

            if($xstmtx->rowCount() == 0) {
                $wa->sendLinkMessage(
                    $ag->paciente_celular,
                    $msg,
                    'https://clinabs.com/',
                    'Financeiro',
                    $data->payment->description,
                    'https://clinabs.com'.Modules::user_get_image($ag->medico_token)
                );
            }
        }else {
            $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_1'");

            if($xstmtx->rowCount() == 0) {
                $wa->sendLinkMessage(
                    $ag->paciente_celular,
                    $msg,
                    'https://clinabs.com/agenda',
                    'Financeiro',
                    $data->payment->description,
                    'https://clinabs.com'.Modules::user_get_image($ag->medico_token)
                );
            }
        }
        

        try {
            $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
            $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 600);
            $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_1');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg}', '{$ag->paciente_celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_2');");
        } catch (PDOException $ex) {
    
        }

        // Médico
        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';
        $dta = date('H:i', strtotime($ag->data_agendamento));
        $date = date('d/m/Y', strtotime($ag->data_agendamento));

        $msg = "Olá *{$prefixo}* *{$ag->medico_nome}*" . PHP_EOL;
        $msg .= "Você tem uma Consulta Agendada com o Paciente *{$user->name}*" . PHP_EOL;
        $msg .= "em {$date} as {$dta}" . PHP_EOL;
        $msg .= "Contato: {$user->mobilePhone}" . PHP_EOL;

        if ($ag->modalidade == 'ONLINE') {
            $meetlink = json_decode($ag->meet)->hostRoomUrl;
        }

        $xstmtx = $pdo->query("SELECT event FROM `CRONTAB` WHERE `event` = '{$event}_2'");

        if($xstmtx->rowCount() == 0) {
            $wa->sendLinkMessage(
                $ag->celular,
                $msg,
                '',
                $data->payment->description,
                ''
            );
        }
        

        $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);

        try {
            $dta = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 3600);
            $dta2 = date('Y-m-d H:i:s', strtotime($ag->data_agendamento) - 600);
            $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_3');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg}', '{$ag->celular}', 'AGENDA_MED', 'PENDENTE', '{$meetlink}', '{$ag->token}', '{$event}_4');");
        } catch (PDOException $ex) {

        }
    } catch (Exception $ex) {

    }
} 

else if ($data->event == 'PAYMENT_DELETED') {
    $paymentType = $data->payment->billingType;
    $paymentStatus = $data->payment->status;

    $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
    $stmt->bindValue(':pm', $paymentType);
    $stmt->bindValue(':sts', 'CANCELADO');
    $stmt->bindValue(':id', $paymentId);


    try {
        $stmt->execute();

        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';

        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'O Dr.' : 'a Dra.';

        $msg = "Olá, a Consulta de *{$user->name}* com *{$prefixo} {$ag->medico_nome}* Foi Cancelada" . PHP_EOL;
        $msg .= '' . PHP_EOL;
        $msg .= "*Valor*: de R$ " . number_format($data->payment->value, 2, ',', '.') . PHP_EOL;
        $msg .= "*Transação:* {$data->payment->id}*" . PHP_EOL;

        $msg .= '*Data do Agendamento:* ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));

        $wa->sendLinkMessage(
            '41995927699',
            $msg,
            'https://clinabs.com/',
            'CLINABS',
            $data->payment->description,
            'https://clinabs.com/assets/images/logo.png'
        );

        $msg = "Olá, a Consulta de *{$user->name}*  Foi Cancelada" . PHP_EOL;
        $msg .= '' . PHP_EOL;

        $msg .= '*Data do Agendamento:* ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));

        $wa->sendLinkMessage(
            $ag->celular,
            $msg,
            'https://clinabs.com/',
            'CLINABS',
            $data->payment->description,
            'https://clinabs.com/assets/images/logo.png'
        );


        $pdo->query("DELETE FROM CRONTAB WHERE agenda_token = '{$ag_token}';");
    } catch (Exception $ex) {

    }
} 

else if ($data->event == 'PAYMENT_UPDATED') {
    $paymentId = $data->payment->id;
    $paymentType = $data->payment->billingType;
    $paymentStatus = $asaas->get_status($data->payment->status);

    $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
    $stmt->bindValue(':pm', $paymentType);
    $stmt->bindValue(':sts', $paymentStatus);
    $stmt->bindValue(':id', $paymentId);
    $stmt->execute();
}

else if($data->event == 'PAYMENT_PARTIALLY_REFUNDED' || $data->event == 'PAYMENT_REFUNDED') {
    $paymentId = $data->payment->id;
    $paymentType = $data->payment->billingType;
    $paymentStatus = $data->payment->status;

    $stmt = $pdo->prepare('UPDATE VENDAS SET `payment_method` = :pm, `status` = :sts WHERE `payment_id` = :id');
    $stmt->bindValue(':pm', $paymentType);
    $stmt->bindValue(':sts', 'CANCELADO');
    $stmt->bindValue(':id', $paymentId);


    try {
        $stmt->execute();

        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';

        $msg = "Olá, a sua consulta com *{$prefixo} {$ag->medico_nome}* Foi Cancelada" . PHP_EOL;
        $msg .= '' . PHP_EOL;
        $msg .= "*Valor*: de R$ " . number_format($data->payment->value, 2, ',', '.') . PHP_EOL;
        $msg .= "*Transação:* {$data->payment->id}*" . PHP_EOL;

        $msg .= '*Data do Agendamento:* ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));

        $wa->sendLinkMessage(
            $ag->paciente_celular,
            $msg,
            'https://clinabs.com/',
            'CLINABS',
            $data->payment->description,
            'https://clinabs.com/assets/images/logo.png'
        );

        $msg = "Olá, a Consulta de *{$user->name}*  Foi Cancelada" . PHP_EOL;
        $msg .= '' . PHP_EOL;

        $msg .= '*Data do Agendamento:* ' . date('d/m/Y H:i', strtotime($ag->data_agendamento));

        $wa->sendLinkMessage(
            $ag->celular,
            $msg,
            'https://clinabs.com/',
            'CLINABS',
            $data->payment->description,
            'https://clinabs.com/assets/images/logo.png'
        );


        $pdo->query("DELETE FROM CRONTAB WHERE agenda_token = '{$ag_token}';");
    } catch (Exception $ex) {

    }
}

echo "Payment info Received Successfully.";