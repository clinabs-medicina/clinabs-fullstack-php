<?php
    require_once '../config.inc.php';
    $tipos = ['EXAME', 'RECEITA', 'LAUDO'];


    $stmt = $pdo->prepare('INSERT INTO `ACOMPANHAMENTO` (`periodo`,`proximo_acompanhamento`, `titulo_acompanhamento`, `funcionario_token`,`paciente_token`, `texto`, `doc_file`, `doc_tipo`) VALUES (:periodo, :proximo_acompanhamento, :titulo_acompanhamento, :funcionario_token, :paciente_token, :texto, :doc_file, :doc_tipo)');
    $stmt->bindValue(':funcionario_token', $_GET['funcionario_token']);
    $stmt->bindValue(':paciente_token', $_REQUEST['paciente_token']);
    $stmt->bindValue(':texto', $_REQUEST['prescricao']);
    $stmt->bindValue(':periodo', in_array($_REQUEST['anexo_tipo'], $tipos) ? '' : $_REQUEST['periodo']);
    $stmt->bindValue(':proximo_acompanhamento', $_REQUEST['proximo_acompanhamento']);
    $stmt->bindValue(':titulo_acompanhamento', $_REQUEST['titulo_acompanhamento']);
    $stmt->bindValue(':doc_file', $_REQUEST['anexo_doc'] ?? '');
    $stmt->bindValue(':doc_tipo', in_array($_REQUEST['anexo_tipo'], $tipos) ? $_REQUEST['anexo_tipo'] : 'ACOMPANHAMENTO');

try {
    $stmt->execute();

    $json = [
        'status' => 'success',
        'icon' => 'success',
        'text' => 'Salvo',
        'result' => $_REQUEST
    ];
}catch(PDOException $ex) {
    
    file_put_contents('a.acompanhamento.error.txt', print_r($ex, true));
    
    $json = [
        'status' => 'danger',
        'icon' => 'danger',
        'text' => 'Erro',
        'reason' => $ex->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);