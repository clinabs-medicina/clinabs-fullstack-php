<?php
require_once '../config.inc.php';

header('Content-Type: application/json');

$resp = $asaas->refund($_GET['payment_id'], $_GET['valor'], $_GET['description']);

echo json_encode($resp, JSON_PRETTY_PRINT);