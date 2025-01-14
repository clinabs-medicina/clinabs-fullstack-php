<?php
$no_debug = true;
require_once('../config.inc.php');

if(isset($_REQUEST['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM `VENDAS` WHERE `VENDAS`.`charge_id` = :id");
    $stmt->bindValue(':id', $_REQUEST['id']);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_OBJ);

    $json =  json_encode($result, JSON_PRETTY_PRINT);
}else{
    $json =  json_encode([
        'message' => 'Nenhuma Chave Recebida'
    ], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json; charset=utf-8');
echo $json;