<?php
require_once '../config.inc.php';
$data = $_REQUEST;

header('Content-Type: application/json');

try {
  $docs = [
    "doc_rg_frente",
    "doc_rg_verso",
    "doc_cpf_frente",
    "doc_cpf_verso",
    "doc_comp_residencia",
    "doc_procuracao",
    "doc_anvisa",
    "doc_termos",
    "doc_receita"
];

$names = [];

foreach($docs as $doc) {
  if(isset($_REQUEST[$doc])) {
    $fname = $_REQUEST[$doc];
    $names[] = $_REQUEST[$doc];

    $stream = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_REQUEST[$doc]);
    file_put_contents($_SERVER['DOCUMENT_ROOT']."/data/images/docs/{$fname}", $stream);
  }
}


if(isset($_REQUEST['doc_receita'])) {
    $stmt = $pdo->prepare('UPDATE FARMACIA SET doc_receita = :doc_receita WHERE token = :token');
    $stmt->bindValue(":doc_receita", $_REQUEST['doc_receita']);
    $stmt->bindValue(":token", $data['token']);
    try {
        $stmt->execute();
    } catch(Exception $ex) {
        echo $ex->getMessage();
    }
} else {
  
  foreach($names as $k => $v) {
    $stmt = $pdo->prepare('UPDATE PACIENTES SET '.$k.' = :doc WHERE token = :token');
    $stmt->bindValue(":doc", $v);
    $stmt->bindValue(":token", $data['paciente']);
    
    try {
        $stmt->execute();
    } catch(Exception $ex) {
        
    }
  }
}

$docs = [
    "doc_rg_frente",
    "doc_rg_verso",
    "doc_cpf_frente",
    "doc_cpf_verso",
    "doc_comp_residencia",
    "doc_procuracao",
    "doc_anvisa",
    "doc_termos",
    "doc_receita"
];
  
  
 echo json_encode([
      'title' => 'Atenção',
      'text' => 'Documentos Atualizados com Sucesso!',
      'icon' => 'success'
    ], JSON_PRETTY_PRINT);
} catch(Exception $ex) {
    file_put_contents('A.error.txt', print_r($ex, true));
    
    echo json_encode([
      'title' => 'Atenção',
      'text' => 'Erro ao Atualizar Documentos!',
      'icon' => 'error',
      'reason' => $ex->getMessage()
    ], JSON_PRETTY_PRINT);
}