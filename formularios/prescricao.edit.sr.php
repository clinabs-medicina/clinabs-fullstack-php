<?php
require_once '../config.inc.php';

$stmt = $pdo->prepare('UPDATE `PRESCRICOES_SR` SET `medico_token` = :medico_token,`paciente_token` = :paciente_token, `prescricao` = :prescricao, `produto_id` = :produto_id, `frascos` = :frascos WHERE id = :id');
$stmt->bindValue(':medico_token', $_REQUEST['medico_token']);
$stmt->bindValue(':paciente_token', $_REQUEST['user_token']);
$stmt->bindValue(':prescricao', $_REQUEST['prescricao']);
$stmt->bindValue(':produto_id', $_REQUEST['produto']);
$stmt->bindValue(':frascos', $_REQUEST['frascos']);
$stmt->bindValue(':id', $_REQUEST['id']);

try {
    $stmt->execute();

    $json = [
        'status' => 'success',
        'icon' => 'success',
        'text' => 'Salvo',
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