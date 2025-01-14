<?php

require_once '../config.inc.php';


if(isset($_REQUEST['token'])) {

    $dados = $pacientes->getPacienteByToken($_REQUEST['token']);
    $dados['data_nascimento'] = date('d/m/Y', strtotime($dados['data_nascimento']));

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);

}else {
    $cpf = preg_replace('/[^0-9]+/', '', $_REQUEST['paciente_cpf']);
    $stmt = $pdo->prepare('SELECT * FROM PACIENTES WHERE cpf = :cpf');
    $stmt->bindValue(':cpf', $cpf);

    try {
        $stmt->execute();

        $dados =  $stmt->fetch(PDO::FETCH_ASSOC);
        $dados['status'] = 'success';
        $dados['data_nascimento'] = date('d/m/Y', strtotime($dados['data_nascimento']));
    } catch (Exception $ex) {
        $dados = ['status' => 'error', 'message' => 'Nenhum Resultado Encontrado.', 'reason' => $ex->getMessage()];
    }

    if($_GET['autologin']) {
        //setcookie('sessid_clinabs_uid', $sessid, time() + 3600, '/', hostname, true);
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_PRETTY_PRINT);
}