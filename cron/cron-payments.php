<?php
require_once '../config.inc.php';

if (strtotime(date('H:i')) >= strtotime('07:00') && strtotime(date('H:i')) <= strtotime('22:00')) {
    $today = date('Y-m-d');

    $payments = $asaas->listarCobrancas('PENDING', date('Y-m-d', strtotime('yesterday')));
}