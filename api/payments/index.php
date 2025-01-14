<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');

$payments = $asaas->getPayments();

foreach($payments as $payment) {
    $payment->customer = $asaas->getClient($payment->customer)->name ?? 'Desconhecido';
}


header('Content-Type: application/json; charset=utf-8');

echo json_encode($payments, JSON_PRETTY_PRINT);