<?php
require_once('../config.inc.php');

$payment = $asaas->getCobranca($_GET['payment_id']);



if(isset($payment->invoiceUrl)) {
    header("Location: {$payment->invoiceUrl}");
} else {
    header("Location: /agenda");
}