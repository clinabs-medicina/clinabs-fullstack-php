<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');

$clientes = $asaas->listarClientes()->data;

$result = [];

foreach($clientes as $cliente) {

    $stmt = $pdo->query("SELECT * FROM PACIENTES WHERE cpf = '{$cliente->cpfCnpj}'");

    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if($stmt->rowCount() > 0) {
        $result[] = $asaas->editarCliente(
            id: $cliente->id,
            token: $row->token, 
            nome: $row->nome_completo, 
            cpf: $row->cpf, 
            email: $row->email, 
            celular: substr($row->celular, 2, 13)
        );
    }

    if(strlen($row->celular) == 13) {
        $cell = substr($row->celular, 2, 13);
        $pdo->query("UPDATE PACIENTES SET celular = '{$cell}' WHERE token = '{$row->token}'");
    }
}

header('Content-Type: application/json; charset=utf-8');

echo json_encode($result, JSON_PRETTY_PRINT);