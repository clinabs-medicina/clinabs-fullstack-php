<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 1);

if($funcionarios->Delete($_REQUEST['token'])) {
    $json = json_encode([
        'title' => 'Atenção',
        'text' => 'Usuário Deletado com Sucesso!',
        'icon' => 'success'
    ]);
}else {
    $json = json_encode([
        'title' => 'Atenção',
        'text' => 'Falha ao Remover Usuário!',
        'icon' => 'error'
    ]);
}

header('Content-Type: application/json');
echo $json;