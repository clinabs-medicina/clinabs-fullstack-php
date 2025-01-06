<?php
require_once('../config.inc.php');

$cobrancas = $asaas->listarCobrancas();

//floor((strtotime($cobranca->dueDate) - time()) / 3600) >= 1
foreach($cobrancas as $cobranca) {
    if(date('Y-m-d', strtotime($cobranca->dueDate)) == date('Y-m-d')) {
        $stmt = $pdo->query("SELECT reference,ultimo_aviso,customer,(SELECT data_cancelamento FROM AGENDA_MED WHERE token = reference) AS data_atualizada, (SELECT data_agendamento FROM AGENDA_MED WHERE token = reference) AS data_agendamento FROM VENDAS WHERE payment_id = '{$cobranca->id}' AND ultimo_aviso = 0");
        $payment = $stmt->fetch(PDO::FETCH_OBJ);

        if($stmt->rowCount() == 1) {
            $data[] = [
                'id' => $cobranca->id,
                'status' => $cobranca->status,
                'invoiceUrl' => $cobranca->invoiceUrl,
                'dueDate' => $cobranca->dueDate,
                'data_agendamento' => $payment->data_agendamento,
                'data_atualizada' => $payment->data_atualizada,
                'timeToDue' => floor((strtotime($payment->data_agendamento) - time()) / 3600),
                'agenda_token' => $payment->reference
            ];

            
            if(floor((strtotime($payment->data_agendamento) - time()) / 3600) < 1) {
                
                try {
                    $payment = $asaas->getCobranca($cobranca->id);
                
                    $info = [];
                    $paciente = $pdo->query("SELECT nome_completo,celular,email,medico_token,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS medico_sexo,(SELECT data_agendamento FROM AGENDA_MED WHERE token = '{$payment->externalReference}') as data_agendamento,(SELECT data_cancelamento FROM AGENDA_MED WHERE token = '{$payment->externalReference}') as data_cancelamento FROM PACIENTES WHERE payment_id = '{$payment->customer}'")->fetch(PDO::FETCH_OBJ);
                    $prefixo = $paciente->medico_sexo == 'Feminino' ? 'Dra.':'Dr.';
                
                    if(strtotime($paciente->data_agendamento) > time()) {
                        $data = date('d/m/Y', strtotime($paciente->data_agendamento)).' as '.date('H:i', strtotime($paciente->data_agendamento));
                    } else {
                        $data = date('d/m/Y', strtotime($paciente->data_cancelamento)).' as '.date('H:i', strtotime($paciente->data_cancelamento));
                    }
                
                    if($payment->status == 'PENDING') {
                        $msg = "Olá *{$paciente->nome_completo}*".PHP_EOL;
                        $msg .= 'Agradecemos o seu contato e agendamento da consulta. Para que possamos manter e confirmar o agendamento, por gentileza efetue o pagamento.';
                        $msg .= "".PHP_EOL;
                        $msg .= 'Consulta com *'.$prefixo.' '.$paciente->medico_nome.'* no dia *'.$data.'*'.PHP_EOL;
                        $msg .= "".PHP_EOL;
                        $msg .= "".PHP_EOL;
                        $msg .= "Você pode utilizar este link para realizar o pagamento!";
                        $msg .= "".PHP_EOL;
                        $msg .= "".PHP_EOL;
                        $msg .= "Fatura: *".trim($payment->invoiceNumber)."*".PHP_EOL;
                        $msg .= "Status: *".trim($asaas->get_status($payment->status))."*".PHP_EOL;
                        $msg .= "Valor: *R$".trim($payment->value)."*".PHP_EOL;
                        $msg .= "Forma de Pagamento: *".$asaas->get_status($payment->billingType)."*".PHP_EOL;
                        $msg .= "Link: {$payment->invoiceUrl}".PHP_EOL;
                        $msg .= "".PHP_EOL;
                        
                        if($payment->billingType == 'PIX') {
                            $msg .= "*Você pode copiar e colar o código para pagamento.*";
                        }
                
                
                        $wa->sendTextMessage(
                            phoneNumber: $paciente->celular,
                            text: $msg
                        );
                    
                        if($payment->billingType == 'PIX') {
                            $pix = $asaas->getPixInfo($payment->id);
                            $pix_msg = $pix->payload;
                    
                            $wa->sendTextMessage(
                                phoneNumber: $paciente->celular,
                                text: $pix->payload
                            );
                        }
                
                        $json = json_encode([
                            'status' => 'success',
                            'title' => 'Atenção',
                            'icon' => 'success',
                            'text' => 'Notificação Enviada com Sucesso.',
                        ], JSON_PRETTY_PRINT);
                    } else {
                        $text = "Olá *{$paciente->nome_completo}*".PHP_EOL;
                        $text .=  "".PHP_EOL;
                        $text .= "Segue o comprovante de Pagamento da Fatura *{$payment->id}*";
                        $invoice = end(explode('/', $payment->transactionReceiptUrl));
                        $link = "https://www.asaas.com/transactionReceipt/pdf/{$invoice}}";
                        $wa->sendDocumentLink('5541995927699','Comprovante de Pagamento', $link, $text);
                
                        $json = json_encode([
                            'status' => 'success',
                            'title' => 'Atenção',
                            'icon' => 'success',
                            'text' => 'Comprovante Enviado com Sucesso.',
                        ], JSON_PRETTY_PRINT);
                    }
                    
                }
                
                catch(Exception $ex) {
                    $json = json_encode([
                        'status' => 'warning',
                        'icon' => 'warning',
                        'text' => 'Não foi Possível Enviar a Notificação!.',
                    ], JSON_PRETTY_PRINT);

                } 
                
                finally {
                    $pdo->query("UPDATE `VENDAS` SET `ultimo_aviso` = '1' WHERE `VENDAS`.`reference` = '{$payment->reference}';");
                }
            }
            
            
        }
    }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);