<?php

require_once('../config.inc.php');

$stmt = $pdo->prepare('DELETE FROM ACOMPANHAMENTO WHERE id = :id');
$stmt->bindValue(':id', $_GET['id']);

try {
    $stmt->execute();

    $json = [
        'status' => 'success',
        'icon' => 'success',
        'text' => 'Deletado',
        'result' => $_GET
    ];
}catch(PDOException $ex) {
    $json = [
        'status' => 'danger',
        'icon' => 'danger',
        'text' => 'Erro'
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json);