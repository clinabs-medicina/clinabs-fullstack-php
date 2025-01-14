<?php
require_once '../config.inc.php';
//error_reporting(0);

$data = $_GET;
$tipo = $data['table'];
$key = $data['key'];
$val = $data['value'];

$tabela = $data['tipo'];

if(isset($data['table']) && isset($data['key']) && isset($data['value']) && !empty($data['value'])) {
    $stmt = $pdo->prepare("SELECT * FROM {$tipo}S WHERE :k = :v");
    $stmt->bindValue(':k', $key);
    $stmt->bindValue(':v', $val);
    
    try {
        $stmt->execute();

        $resp = [
            'status' => ($stmt->rowCount() > 0 ? 'warning':'success'),
            'text' => 'Já Cadastrado'
        ];
    }catch(Exception $e) {
        $resp = [
            'status' => 'error',
            'reason' =>  'Verifique os parãmetros POST {table},{key},{value} se estão corretos'
        ];
    }
} else {
    $resp = [
        'status' => 'error',
        'reason' =>  'Você precisa enviar os dados nos parãmetros POST {table},{key},{value}'
    ];
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($resp, JSON_PRETTY_PRINT);