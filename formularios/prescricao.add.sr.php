<?php
require_once '../config.inc.php';
file_put_contents('A.dados.prescricao.sr.txt', print_r($_REQUEST, true));


if( $_REQUEST['frascos'] > 0){
    $stmt = $pdo->prepare('INSERT INTO `PRESCRICOES_SR` (`medico_token`,`paciente_token`, `prescricao`, `produto_id`, `frascos`) VALUES (:medico_token, :paciente_token, :prescricao, :produto_id, :frascos)');
    $stmt->bindValue(':medico_token', $_REQUEST['medico_token']);
    $stmt->bindValue(':paciente_token', $_REQUEST['paciente_token']);
    $stmt->bindValue(':prescricao', $_REQUEST['prescricao']);
    $stmt->bindValue(':produto_id', $_REQUEST['produto']);
    $stmt->bindValue(':frascos', $_REQUEST['frascos']);
}else {
    $stmt = $pdo->prepare('INSERT INTO `PRESCRICOES_SR` (`funcionario_token`,`paciente_token`, `prescricao`) VALUES (:funcionario_token, :paciente_token, :prescricao)');
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
    file_put_contents('A.erro.prescricao.sr.txt', print_r($ex, true));
    
    $json = [
        'status' => 'danger',
        'icon' => 'danger',
        'text' => 'Erro'
    ];
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);