<?php
require_once '../config.inc.php';

$data = $_REQUEST;
$stmt = $pdo->prepare("SELECT * FROM MEDICOS WHERE id =:id");
$stmt->bindValue(':id', $data['id']);

        
    try {
        $stmt->execute();
        $medico = $stmt->fetch(PDO::FETCH_OBJ);

        $resp = [
            'nome' => $medico->nome_completo
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp, JSON_PRETTY_PRINT);
    } catch(Exception $ex) {
        $resp = [
            'status' => []
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp, JSON_PRETTY_PRINT);
    }