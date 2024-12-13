<?php
global $pacientes;
require_once '../config.inc.php';

try{
    $usuario = $pacientes->getPacienteByToken($_REQUEST['token']);

    //setcookie('sessid_clinabs_uid', $usuario->token, time() + 63072000, '/', hostname, true);

    $_SESSION['sessid_clinabs_uid'] = md5($usuario->token);

    session_start();
    // Regenera o ID da sessão para segurança (após o session_start)
    session_regenerate_id(true);


    $json = json_encode([
        'status' => 'success',
    ]);
}catch(Exception $ex)
{
    $json = json_encode([
        'status' => 'error',
    ]);
}

header('content-type: application/json');
echo $json;