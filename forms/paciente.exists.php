<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';


if(isset($_GET['cpf'])) {
    $stmt = $pdo->prepare('SELECT * FROM PACIENTES WHERE cpf = :cpf');
    $stmt->bindValue(':cpf', preg_replace('/[^0-9]+/', '', $_REQUEST['cpf']));

    try {
        $stmt->execute();

        $result = ['exists' => $stmt->rowCount() > 0, 'data' => $stmt->fetch(PDO::FETCH_ASSOC)];
    } catch(Exception $ex) {
        $result = ['error' => 'Erro ao Validar Usuário'];
    }
} else if(isset($_GET['email'])) {
    $stmt = $pdo->prepare('SELECT * FROM PACIENTES WHERE LOWER(email) = :email');
    $stmt->bindValue(':email', strtolower($_REQUEST['email']));

    try {
        $stmt->execute();

        $result = ['exists' => $stmt->rowCount() > 0];
    } catch(Exception $ex) {
        $result = ['error' => 'Erro ao Validar Usuário'];
    }
}

else if(isset($_GET['celular'])) {
    $stmt = $pdo->prepare('SELECT * FROM PACIENTES WHERE celular = :celular');
    $stmt->bindValue(':celular', preg_replace('/[^0-9]+/', '', $_REQUEST['celular']));

    try {
        $stmt->execute();

        $result = ['exists' => $stmt->rowCount() > 0];
    } catch(Exception $ex) {
        $result = ['error' => 'Erro ao Validar Usuário'];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);