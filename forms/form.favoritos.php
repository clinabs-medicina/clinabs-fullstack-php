<?php

session_start();
require_once '../config.inc.php';
ini_set('display_errors', 1);
error_reporting(1);
$items = [];

function SumQtde($items) {
    $total = 0;

    foreach($items as $item) {
        $total += $item->qtde;
    }

    return $total;
}

function SumVal($items) {
    $total = 0;

    foreach($items as $item) {
        $total += ($item->valor * $item->qtde);
    }

    return $total;
}

if(isset($_REQUEST['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode($favoritos->add($_REQUEST['product_id'],isset($_REQUEST['pid']) ? $_REQUEST['pid'] : uniqid(), $user->cpf));
} else if(isset($_REQUEST['remove']) && isset($_REQUEST['pid'])) {
    header('Content-Type: application/json');
    echo json_encode($favoritos->removeItem($_REQUEST['pid'], $user->cpf) > 0 ? ['status' => 'success'] : ['status' => 'danger', 'exception' => $favoritos->lastException->getMessage()]);
}else {
    header('Content-Type: application/json');
    echo json_encode(['total' => count($favoritos->getAll($user->cpf)) ], 16 | 128);
}