<?php
require_once('../config.inc.php');
file_put_contents('logs/log.txt', print_r($_REQUEST, true));
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['userObj'])) {
    $user = (object) $_SESSION['userObj'];
}

$stmt = $pdo->prepare("INSERT INTO `MEDICAMENTOS` (`nome`, `user_token`, `medico_token`, `unidade_medida`, `conteudo`, `tipo_conteudo`) VALUES (:nome, :user_token, :medico_token, :unidade_medida, :conteudo, :tipo_conteudo)");

$data = $_REQUEST;
$stmt->bindValue(':nome', $data['nome']);
$stmt->bindValue(':user_token', $user->token);
$stmt->bindValue(':medico_token', $data['medico']);
$stmt->bindValue(':unidade_medida', $data['medida']);
$stmt->bindValue(':conteudo', $data['conteudo']);
$stmt->bindValue(':tipo_conteudo', $data['tipo_conteudo']);

try {
    $stmt->execute();
    $result = [
        'status' => 'success',
        'icon' => 'success',
        'text' => 'Medicamento Cadastrado com Sucesso!'
    ];
} catch (PDOException $ex) {
    $result = [
        'status' => 'error',
        'icon' => 'error',
        'text' => $ex->getMessage()
    ];
}


header('Content-Type: application/json');
echo json_encode($result);