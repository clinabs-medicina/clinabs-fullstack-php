<?php
    require_once '../config.inc.php';

    $tipos = ['EXAME', 'RECEITA', 'LAUDO'];
    
    $stmt = $pdo->prepare('INSERT INTO `ACOMPANHAMENTO` (`periodo`,`semana`,`funcionario_token`,`paciente_token`, `texto`, `doc_file`, `doc_tipo`) VALUES (:periodo, :semana, :funcionario_token, :paciente_token, :texto, :doc_file, :doc_tipo)');
    $stmt->bindValue(':funcionario_token', $_GET['funcionario_token']);
    $stmt->bindValue(':paciente_token', $_REQUEST['paciente_token']);
    $stmt->bindValue(':texto', $_REQUEST['prescricao']);
    $stmt->bindValue(':periodo', in_array($_REQUEST['anexo_tipo'], $tipos) ? '' : $_REQUEST['periodo']);
    $stmt->bindValue(':semana', in_array($_REQUEST['anexo_tipo'], $tipos) ? '' : $_REQUEST['semana']);
    $stmt->bindValue(':doc_file', $_REQUEST['anexo_doc'] ?? '');
    $stmt->bindValue(':doc_tipo', $_REQUEST['anexo_tipo'] ?? '');

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