<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$id = $_GET['medico_id'];
$token = $_GET['medico_token'];

$stmt = $pdo->query("SELECT nome,logradouro,numero,cidade,uf,bairro FROM `ENDERECOS` WHERE tipo_endereco = 'ATENDIMENTO' AND user_token = '{$token}' UNION SELECT nome,logradouro,numero,cidade,uf,bairro FROM `UNIDADES` WHERE medicos LIKE '%\"{$id}\"%'");
$unidades = $stmt->fetchAll(PDO::FETCH_OBJ);

header('Content-Type: application/json');
echo json_encode($unidades, JSON_PRETTY_PRINT);