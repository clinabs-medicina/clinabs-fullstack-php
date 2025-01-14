<?php
require_once '../config.inc.php';

$data = $_REQUEST;

$stmt = $pdo->prepare("SELECT * FROM ENDERECOS WHERE user_token = :user_token");
$stmt->bindValue(':user_token', $data['medico_token']);


try {
    $stmt->execute();

    $result['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch(Exception $ex) {
    $result = [];
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);