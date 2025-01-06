<?php
require_once '../config.inc.php';

$dados = json_decode(json_encode($pacientes->getPacienteByCpf($_REQUEST['cpf'])), true);
$dados['profile_image'] = Modules::getUserImage($dados->token);

foreach(json_decode($dados['enderecos'], true) as $street) {
    if($street['isDefault']) {
        foreach($street as $k => $v) {
            $dados[$k] = $v;
        }

        break;
    }
}

unset($dados['enderecos']);


header('Content-Type: application/json');

if(isset($dados['cpf'])) {
    echo json_encode([
        'status' => 'warning',
        'text' => 'Paciente já Cadastrado Deseja Continuar?',
        'paciente' => $dados['token'],
        'data' => $dados
    ], 16|64|128|256);
}else{
    echo json_encode([
        'status' => 'success',
        'text' => 'Paciente não Cadastrado',
    ], 16|64|128|256);
}