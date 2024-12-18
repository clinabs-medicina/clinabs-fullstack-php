<?php
require_once '../config.inc.php';
file_put_contents('last_sync_cron_payment.txt', date('Y-m-d H:i:s'));

if (strtotime(date('H:i')) >= strtotime('07:00') && strtotime(date('H:i')) <= strtotime('22:00')) {
    $today = date('Y-m-d');

    $payments = $asaas->listarCobrancas('PENDING', date('Y-m-d', strtotime('yesterday')));
}