<?php
global $pacientes;
require_once '../config.inc.php';

try{
    $usuario = $pacientes->getPacienteByToken($_REQUEST['token']);

    //setcookie('sessid_clinabs_uid', $usuario->token, time() + 63072000, '/', hostname, true);

    $json = json_encode([
        'status' => 'success',
    ]);
}catch(Exception $ex)
{
    $json = json_encode([
        'status' => 'error',
    ]);
}

header('Content-Type: application/json; charset=utf-8');
echo $json;