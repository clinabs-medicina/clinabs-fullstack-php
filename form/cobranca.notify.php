<?php
require_once '../config.inc.php';

try {
    $payment = $asaas->getCobranca($_GET['token']);

    $info = [];

    $paciente = $pdo->query("SELECT PACIENTES.nome_completo, PACIENTES.celular, PACIENTES.email, MEDICOS.nome_completo AS medico_nome,MEDICOS.identidade_genero AS medico_sexo, VENDAS.payment_id, AGENDA_MED.data_agendamento, AGENDA_MED.medico_token FROM AGENDA_MED, MEDICOS, PACIENTES, VENDAS WHERE MEDICOS.token = AGENDA_MED.medico_token AND PACIENTES.token = AGENDA_MED.paciente_token AND VENDAS.reference = AGENDA_MED.token AND AGENDA_MED.token = '{$payment->externalReference}'")->fetch(PDO::FETCH_OBJ);
    
    
    $prefixo = strtolower($paciente->medico_sexo) == 'feminino' ? 'Dra.':'Dr.';

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
    
}catch(Exception $ex) {
    $json = json_encode([
        'status' => 'warning',
        'icon' => 'warning',
        'text' => 'Não foi Possível Enviar a Notificação!.\n\n'.$ex->getMessage(),
    ], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json');
echo $json;