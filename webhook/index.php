<?php
error_reporting(1);
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');
ini_set('display_errors', 1);


$data = file_get_contents("php://input");

$payload = json_decode($data);

file_put_contents("./logs/{$payload->event}_{$payload->payment->billingType}.json", json_encode($payload, JSON_PRETTY_PRINT));


function get_paciente($agendamentoId) {
    $servername = 'localhost';
    $database = 'clinabs_homolog';
    $username = 'clinabs_dev';
    $password = '&?7z?Yw$0]62N!gbn=l_@bbA0O{TRg:s';

    // Banco de Dados
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection Failed: " . $e->getMessage();
    }

    $stmt = $pdo->query("SELECT data_agendamento,duracao_agendamento, meet, modalidade, tipo_agendamento, ( SELECT nome_completo FROM PACIENTES WHERE token = paciente_token ) AS paciente_nome, ( SELECT celular FROM PACIENTES WHERE token = paciente_token ) AS paciente_celular, ( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS medico_nome, ( SELECT celular FROM MEDICOS WHERE token = medico_token ) AS medico_celular, ( SELECT identidade_genero FROM MEDICOS WHERE token = medico_token ) AS medico_sexo, ( SELECT ( SELECT nome FROM ESPECIALIDADES WHERE id = especialidade ) AS especialidade FROM MEDICOS WHERE token = medico_token ) AS especialidade FROM AGENDA_MED WHERE token = '{$agendamentoId}'");
    $obj =  $stmt->fetch(PDO::FETCH_ASSOC);

    if($stmt->rowCount() == 1) {
        $prefixo = strtoupper($obj['medico_sexo']) == 'MASCULINO' ? 'Dr':'Dra';
        $obj['medico_nome'] = "{$prefixo}. {$obj['medico_nome']}";

        unset($obj['medico_sexo']);

        $meet = json_decode($obj['meet']);

       unset($obj['meet']);

        $obj['meetingId'] = 'https://clinabs.com/teleconsulta'.$meet->roomName;
        $obj['meet_allowed_interval'] = [
            'start' => date('Y-m-d H:i:s', strtotime($obj['data_agendamento']) - 600),
            'end' => date('Y-m-d H:i:s', strtotime($obj['data_agendamento']) + ($obj['duracao_agendamento'] * 60) + 600)
        ];
    }

    return $obj;
}

if($payload->event == 'PAYMENT_RECEIVED') {
    if($payload->payment->billingType == 'CREDIT_CARD' || $payload->payment->billingType == 'DEBIT_CARD' || $payload->event == 'PIX') {
        $userInfo = get_paciente($payload->payment->externalReference);

        $payload = [
            'event' => $payload->event,
            'status' => $payload->payment->status,
            'value' => $payload->payment->value,
            'type' => $payload->payment->billingType,
            'agendamentoToken' => $payload->payment->externalReference,
            'customer' => $payload->payment->customer,
            'payment_id' => $payload->payment->id,
            'description' => $payload->payment->description,
            'dueDate' => $payload->payment->dueDate,
            'paymentDate' => $payload->payment->paymentDate,
            'clientPaymentDate' => $payload->payment->clientPaymentDate,
            'invoiceUrl' => $payload->payment->invoiceUrl,
            'invoiceNumber' => $payload->payment->invoiceNumber,
            'receiptUrl' => $payload->payment->transactionReceiptUrl,
            'lastViewed' => $payload->payment->lastInvoiceViewedDate,
            'pacienteInfo' => $userInfo
        ];


        $wa->sendLinkMessage(
            '41995927699',
            '[TESTE] Olá *' . $userInfo['paciente_nome'] . '*' . PHP_EOL . 'O Pagamento referente a Consulta  com ' . $userInfo['medico_nome']. ' no dia ' . date('d/m/Y H:i', strtotime($userInfo['data_agendamento'])) . ' no valor R$ ' . number_format($payload['value'], 2, ',', '.') . ' foi confirmado com Sucesso!'.PHP_EOL.PHP_EOL."Link da Teleconsulta: {$userInfo['meetingId']}",
            'https://clinabs.com/',
            'Financeiro',
            $payload['description'],
            'https://clinabs.com/assets/images/logo.png'
        );
    }
} 

else if($payload->event == 'PAYMENT_UPDATED') {
    $payload = [
        'event' => $payload->event,
        'status' => $payload->payment->status,
        'value' => $payload->payment->value,
        'type' => $payload->payment->billingType,
        'agendamentoToken' => $payload->payment->externalReference,
        'customer' => $payload->payment->customer,
        'payment_id' => $payload->payment->id,
        'description' => $payload->payment->description,
        'dueDate' => $payload->payment->dueDate,
        'paymentDate' => $payload->payment->paymentDate,
        'clientPaymentDate' => $payload->payment->clientPaymentDate,
        'invoiceUrl' => $payload->payment->invoiceUrl,
        'invoiceNumber' => $payload->payment->invoiceNumber,
        'receiptUrl' => $payload->payment->transactionReceiptUrl,
        'lastViewed' => $payload->payment->lastInvoiceViewedDate,
        'pacienteInfo' => get_paciente($payload->payment->externalReference)
    ];
} 

else if($payload->event == 'PAYMENT_OVERDUE') {
    $payload = [
        'event' => $payload->event,
        'status' => $payload->payment->status,
        'value' => $payload->payment->value,
        'type' => $payload->payment->billingType,
        'agendamentoToken' => $payload->payment->externalReference,
        'customer' => $payload->payment->customer,
        'payment_id' => $payload->payment->id,
        'description' => $payload->payment->description,
        'dueDate' => $payload->payment->dueDate,
        'paymentDate' => $payload->payment->paymentDate,
        'clientPaymentDate' => $payload->payment->clientPaymentDate,
        'invoiceUrl' => $payload->payment->invoiceUrl,
        'invoiceNumber' => $payload->payment->invoiceNumber,
        'receiptUrl' => $payload->payment->transactionReceiptUrl,
        'lastViewed' => $payload->payment->lastInvoiceViewedDate,
        'pacienteInfo' => get_paciente($payload->payment->externalReference)
    ];
} 

else if($payload->event == 'PAYMENT_DELETED') {
    $payload = [
        'event' => $payload->event,
        'status' => $payload->payment->status,
        'value' => $payload->payment->value,
        'type' => $payload->payment->billingType,
        'agendamentoToken' => $payload->payment->externalReference,
        'customer' => $payload->payment->customer,
        'payment_id' => $payload->payment->id,
        'description' => $payload->payment->description,
        'dueDate' => $payload->payment->dueDate,
        'paymentDate' => $payload->payment->paymentDate,
        'clientPaymentDate' => $payload->payment->clientPaymentDate,
        'invoiceUrl' => $payload->payment->invoiceUrl,
        'invoiceNumber' => $payload->payment->invoiceNumber,
        'receiptUrl' => $payload->payment->transactionReceiptUrl,
        'lastViewed' => $payload->payment->lastInvoiceViewedDate,
        'pacienteInfo' => get_paciente($payload->payment->externalReference)
    ];
}


header('Content-Type: application/json');
echo json_encode($payload, JSON_PRETTY_PRINT);