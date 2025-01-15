<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');

$ss = $pdo->query("SELECT payment_id FROM `VENDAS` WHERE payment_id LIKE 'pay_%';");
$payments = $ss->fetchAll(PDO::FETCH_OBJ);

foreach ($payments as $payment) {
    $pay = $asaas->getCobranca($payment->payment_id);

    try {
        $xpay = $pdo->prepare('UPDATE VENDAS SET `asaas_payload` = :payload WHERE `payment_id` = :id');
        $xpay->bindValue(':payload', json_encode($pay, JSON_PRETTY_PRINT));
        $xpay->bindValue(':id', $payment->payment_id);
        $xpay->execute();
    } catch (Exception $ex) {
    }
}
