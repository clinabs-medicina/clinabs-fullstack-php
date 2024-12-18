<?php
require_once '../config.inc.php';
ini_set('display_errors', 1);
error_reporting(1);

$action = $_REQUEST['action'];
$token = $_REQUEST['token'];

$stmt = $pdo->prepare('SELECT * FROM VENDAS WHERE reference = :token');
$stmt->bindValue(':token', $token);

$json = [];

$stmt->execute();
$venda = $stmt->fetch(PDO::FETCH_OBJ);

$json['request'] = $_REQUEST;
$json['response'] = $venda;


switch($action) {
    case 'order-cancel': {
        $sts = 'CANCELAMENTO PENDENTE';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

        
            $json = [
                'status' => 'success',
                'text' => 'Pedido Cancelado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Cancelar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'order-undo': {
        $sts = $venda->paid == 1 ? 'PAGO':'AGUARDANDO PAGAMENTO';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

        
            $json = [
                'status' => 'success',
                'text' => 'Pedido Cancelado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Cancelar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'order-accept': {
        $sts = 'PAGO';

        try {
            $stmt = $pdo->prepare('UPDATE VENDAS SET `status` = :sts, paid = 1 WHERE reference = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

            $json = [
                'status' => 'success',
                'text' => 'Pedido Aterado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'newStatus' => 'PAGO',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Alterar Pedido!',
                'icon' => 'error',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
        }

        break;
    }
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);