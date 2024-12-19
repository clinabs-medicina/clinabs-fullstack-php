<?php
$no_debug = true;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$id = $_GET['medico_id'];
$token = $_GET['medico_token'];

$stmt = $pdo->query("SELECT nome,logradouro,numero,cidade,uf,bairro,token,inicio_expediente,fim_expediente FROM `ENDERECOS` WHERE tipo_endereco = 'ATENDIMENTO' AND user_token = '{$token}' UNION SELECT nome,logradouro,numero,cidade,uf,bairro,token,inicio_expediente,fim_expediente FROM `UNIDADES` WHERE medicos LIKE '%\"{$id}\"%'");
$unidades = $stmt->fetchAll(PDO::FETCH_OBJ);

header('Content-Type: application/json');


if(isset($_GET['time'])) {
    $results = [];

    foreach($unidades as $unidade) {
        if($unidade->inicio_expediente <= $_GET['time'] && $unidade->fim_expediente >= $_GET['time']) {
            $results[] = $unidade;
        }
    }

    echo json_encode($results, JSON_PRETTY_PRINT);
} else {
    echo json_encode($unidades, JSON_PRETTY_PRINT);
}