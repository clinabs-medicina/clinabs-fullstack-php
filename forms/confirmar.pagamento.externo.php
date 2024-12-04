<?php
require_once('../config.inc.php');

if(isset($_POST['payments'])) {
    $request = $_POST;
} else {
    $request = file_get_contents('php://input', 'r');
}

try {
    $payment = $asaas->receber_dinheiro($request['payment_id']);
    $details = json_encode($request['payments'], JSON_PRETTY_PRINT);
    $stmt = $pdo->prepare("UPDATE `VENDAS` SET `details` = :details WHERE payment_id = :payment_id");
    $stmt->bindValue(':details', $details);
    $stmt->bindValue(':payment_id', $request['payment_id']);

    $stmt->execute();

    $json = json_encode([
        'status' => 'success',
        'title' => 'Atenção',
        'icon' => 'success',
        'text' => 'Pagamento Realizado com Sucesso!',
    ], JSON_PRETTY_PRINT);
} catch(Exception $ex) {
    $json = json_encode([
        'status' => 'warning',
        'icon' => 'warning',
        'text' => 'Não foi Processar o Pagamento!.',
    ], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json');
echo $json;
