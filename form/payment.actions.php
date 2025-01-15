<?php
require_once ('../config.inc.php');

$data = $_REQUEST;

file_put_contents('ag-log.txt', print_r($data, true));

if ($data['action'] == 'delete_payment') {
    $stmt = $pdo->prepare("UPDATE VENDAS SET status = 'CANCELADO' WHERE id = :id");
    $stmt->bindValue(':id', $data['id']);
    try {
        $stmt->execute();

        $json = [
            'status' => 'success',
            'message' => 'Pagamento cancelado com sucesso',
            'icon' => 'success',
        ];
    } catch (Exception $e) {
        $json = [
            'status' => 'error',
            'message' => 'Erro ao cancelar pagamento',
            'icon' => 'error',
        ];
    }
} else if ($data['action'] == 'confirm_payment') {
    $stmt = $pdo->prepare("UPDATE VENDAS SET status = 'AGENDADO' WHERE id = :id");
    $stmt->bindValue(':id', $data['id']);

    try {
        $stmt->execute();

        $json = [
            'status' => 'success',
            'message' => 'Pagamento confirmado com sucesso',
            'icon' => 'success',
        ];
    } catch (Exception $e) {
        $json = [
            'status' => 'error',
            'message' => 'Erro ao confirmar pagamento',
            'icon' => 'error',
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($json);
