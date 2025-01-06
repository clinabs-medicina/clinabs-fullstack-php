<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }

global $carrinho;
require_once '../config.inc.php';

ini_set('display_errors', 1);
error_reporting(1);


if(isset($_REQUEST['qtde']) && isset($_REQUEST['pid'])) {
    $qtde = $_REQUEST['qtde'];
    $pid = $_REQUEST['pid'];

    $produto = $carrinho->getByPid($pid, $user->cpf);

    $item = json_decode(json_encode($produto), true);
    $carrinho->update($pid, $qtde);
    $produto = $carrinho->getByPid($pid, $user->cpf);
    $c = new CarrinhoCalc($pdo);
    $prod = $c->getProdByPromo($produto->id, $produto->qtde);
    $frete = $prod['valor_frete'];
    
    $item['calc'] = array(
        'total' => $prod['valor_total'],
        'parcela' => round($prod['valor_total'] / 11)
    ); 
    
    $item['valor'] = $prod['valor'];
    $item['frete'] = $frete;
    $item['total'] = $prod['valor_total'];
    header('Content-Type: application/json');
    echo json_encode($item, JSON_PRETTY_PRINT);
}