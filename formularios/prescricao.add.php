<?php
require_once '../config.inc.php';

if( $_REQUEST['frascos'] > 0){
    $stmt = $pdo->prepare('INSERT INTO `PRESCRICOES` (`medico_token`,`paciente_token`, `agenda_token`, `prescricao`, `produto_id`, `frascos`, `produto_ref`) VALUES (:medico_token, :paciente_token, :agenda_token, :prescricao, :produto_id, :frascos, :reference)');
    $stmt->bindValue(':medico_token', $_REQUEST['medico_token']);
    $stmt->bindValue(':paciente_token', $_REQUEST['paciente_token']);
    $stmt->bindValue(':agenda_token', $_REQUEST['agenda_token']);
    $stmt->bindValue(':prescricao', $_REQUEST['prescricao']);
    $stmt->bindValue(':produto_id', $_REQUEST['produto']);
    $stmt->bindValue(':frascos', $_REQUEST['frascos']);
    $stmt->bindValue(':reference', $_REQUEST['reference']);
}else {
    $stmt = $pdo->prepare('INSERT INTO `PRESCRICOES` (`funcionario_token`,`paciente_token`, `prescricao`) VALUES (:funcionario_token, :paciente_token, :prescricao)');
    $stmt->bindValue(':funcionario_token', $usert->token);
    $stmt->bindValue(':paciente_token', $_REQUEST['paciente_token']);
    $stmt->bindValue(':prescricao', $_REQUEST['prescricao']);
}

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