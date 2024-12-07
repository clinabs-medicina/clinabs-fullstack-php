<?php
require_once '../config.inc.php';
file_put_contents('logs/dados.edit.prescricao.txt', print_r($_REQUEST, true));
$stmt = $pdo->prepare('UPDATE `PRESCRICOES` SET `medico_token` = :medico_token,`paciente_token` = :paciente_token, `agenda_token` = :agenda_token, `prescricao` = :prescricao, `produto_id` = :produto_id, `frascos` = :frascos WHERE id = :id');
$stmt->bindValue(':medico_token', $_REQUEST['medico_token']);
$stmt->bindValue(':paciente_token', $_REQUEST['user_token']);
$stmt->bindValue(':agenda_token', $_REQUEST['agenda_token']);
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
    file_put_contents('logs/erro.prescricao.txt', print_r($ex, true));
    
    $json = [
        'status' => 'danger',
        'icon' => 'danger',
        'text' => 'Erro',
        'exception' => $ex->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);