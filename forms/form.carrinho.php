<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['userObj'])) {
    $user = (object) $_SESSION['userObj'];
}

// Regenera o ID da sessão para segurança (após o session_start)
session_regenerate_id(true);
require_once '../config.inc.php';
ini_set('display_errors', 1);
error_reporting(1);
$items = [];
$frete = 180;


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
    echo json_encode($carrinho->add($_REQUEST['product_id'],isset($_REQUEST['pid']) ? $_REQUEST['pid'] : uniqid(), $user->cpf, $_SESSION['token']));
} else if(isset($_REQUEST['remove']) && isset($_REQUEST['pid'])) {
    header('Content-Type: application/json');
    echo json_encode($carrinho->removeItem($_REQUEST['pid'], $_SESSION['token']) > 0 ? ['status' => 'success'] : ['status' => 'danger', 'exception' => $carrinho->lastException->getMessage()]);
}
else{
    $items['objs'] = $carrinho->getAll($_SESSION['token']);
    $items['details'] = [
        'sum' => SumQtde($items['objs']),
        'valorTotal' => (SumVal($items['objs'])),
        'total' => (SumVal($items['objs']) + $frete)
    ];

    header('Content-Type: application/json');
    echo json_encode($items, 64|128|256);
}
