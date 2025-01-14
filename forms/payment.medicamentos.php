<?php
require_once('../config.inc.php');

$id = $_REQUEST['id'];
$action = $_REQUEST['action'];

if ($action == 'confirm') {
    $stmt = $pdo->prepare('UPDATE VENDAS SET status = :status WHERE id = :id');
    $stmt->bindValue(':status', 'PAGO');
    $stmt->bindValue(':id', $id);
    
    try {
        $stmt->execute();
        $resp = [
            'status' => 'success',
            'message' => 'Pagamento confirmado com sucesso',
            'id' => $id
        ];

    } catch (Exception $e) {
        $resp = [
            'status' => 'error',
            'message' => 'Erro ao confirmar o pagamento'
        ];
    }
} else if ($action == 'cancel') {
    $stmt = $pdo->prepare('UPDATE VENDAS SET status = :status WHERE id = :id');
    $stmt->bindValue(':status', 'CANCELADO');
    $stmt->bindValue(':id', $id);
    
    try {
        $stmt->execute();
        $resp = [
            'status' => 'success',
            'message' => 'Pagamento cancelado com sucesso',
            'id' => $id
        ];

    } catch (Exception $e) {
        $resp = [
            'status' => 'error',
            'message' => 'Erro ao cancelar o pagamento'
        ];
    }
} else if ($action == 'delete') {
    $stmt = $pdo->prepare('DELETE FROM VENDAS WHERE id = :id');
    $stmt->bindValue(':id', $id);
    
    try {
        $stmt->execute();
        $resp = [
            'status' => 'success',
            'message' => 'Pagamento cancelado com sucesso',
            'id' => $id
        ];

    } catch (Exception $e) {
        $resp = [
            'status' => 'error',
            'message' => 'Erro ao cancelar o pagamento'
        ];
    }
} else {
    $resp = [
        'status' => 'error',
        'message' => 'Ação inválida',
        'request' => $_REQUEST
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resp);