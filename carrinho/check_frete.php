<?php
require_once '../config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$produtos_carrinho = $carrinho->getAll($_SESSION['token']);

header('Content-Type: application/json; charset=utf-8');

$valor = 0;
$total = 0;

foreach($produtos_carrinho as $produto) {
    $c = new CarrinhoCalc($pdo);
    $prod = $c->getProdByPromo($produto->id, $produto->qtde);

   
   $valor += ($prod['valor']);
}

foreach($produtos_carrinho as $produto) {
    $c = new CarrinhoCalc($pdo);
    $prod = $c->getProdByPromo($produto->id, $produto->qtde);
    
    $total += ($produto->valor);
    
   if($prod['valor_frete'] == 0) {
       $frete = 0;
   }
}

$vt = ($valor + $frete);


echo json_encode([
    'frete' => $frete,
    'valor_produtos' => ($valor),
    'total_produtos' => $vt
    ], JSON_PRETTY_PRINT);
