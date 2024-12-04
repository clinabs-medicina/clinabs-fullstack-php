<?php
require_once '../config.inc.php';



if(isset($_GET['paymentCode'])) {
    $payment = $asaas->getCobranca($_GET['paymentCode']);
    $pix = ['status' => $payment->status];
    echo json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}