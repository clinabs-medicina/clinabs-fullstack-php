<?php
require_once('../config.inc.php');

$stmt = $pdo->prepare('SELECT payment_id, details FROM VENDAS WHERE payment_id = :payment_id');
$stmt->bindValue(':payment_id', $_GET['payment_id']);

try {
    $stmt->execute();

    $item = $stmt->fetch(PDO::FETCH_OBJ);
    $pay = json_decode($item->details);

    $json = json_encode([
        'title' => 'Informações de Pagamento',
        'icon' => 'info',
        'data' => $item
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);

} catch(Exception $ex) {
    $json = json_encode(['title' => 'Atenção', 'icon' => 'error', 'text' => 'Ocorreu um Erro ao Buscar as Informaçãoes.'], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json; charset=utf-8');
echo $json;