<?php
require_once '../config.inc.php';

$stmt = $pdo->prepare('DELETE FROM `PRESCRICOES` WHERE id = :id');
$stmt->bindValue(':id', $_REQUEST['id']);

try {
    $stmt->execute();

    $json = [
        'status' => 'success',
        'icon' => 'success',
        'text' => 'Deletado',
        'result' => $_REQUEST
    ];
}catch(PDOException $ex) {
    $json = [
        'status' => 'danger',
        'icon' => 'danger',
        'text' => 'Erro'
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json, JSON_PRETTY_PRINT);