<?php

require_once '../config.inc.php';

$stmt = $pdo->prepare('SELECT * FROM PACIENTES WHERE cpf = :cpf');
$stmt->bindValue(':cpf', preg_replace("/[^A-Za-z0-9]/", "", $_REQUEST['cpf']));

try {
    $stmt->execute();

    $res = $stmt->rowCount() > 0;
} catch(Exception $ex) {
    $res =  false;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['exists' => $res], JSON_PRETTY_PRINT);