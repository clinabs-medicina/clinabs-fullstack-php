<?php
global $pacientes;
require_once '../config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try{
    $usuario = $pacientes->getPacienteByToken($_REQUEST['token']);
    $_SESSION['token'] = $usuario->token;    
    setcookie('sessid_clinabs_uid', $usuario->token, time() + 63072000, '/', hostname, true);

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