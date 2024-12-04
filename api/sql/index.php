<?php
set_time_limit(0);
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$data = [];

require_once("{$doc_root}/config.inc.php");

function uuid_gen(){
  return uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid();
}

function copy_file($src, $uuid, $dst) {
  if (!file_exists($src)) {
    return false;
  } else {
      $ext = pathinfo($src, PATHINFO_EXTENSION);
      $dst = $dst . '/' . $uuid . '.' . $ext;
      copy($src, $dst);

      return file_exists($dst);
  }
}

$stmt = $pdo->query("SELECT `id`, 
`token`, 
`nome_completo`, 
`doc_rg_frente`, 
`doc_rg_verso`, 
`doc_cpf_frente`,
`doc_cpf_verso`, 
`doc_comp_residencia`, 
`doc_procuracao`,
`doc_anvisa`, 
`doc_termos`
FROM 
`PACIENTES`");

$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($pacientes as $paciente) {

  $items = [
      'doc_rg_frente',
      'doc_rg_verso',
      'doc_cpf_frente',
      'doc_cpf_verso',
      'doc_comp_residencia',
      'doc_procuracao',
      'doc_anvisa',
      'doc_termos'
    ];
  
  foreach($items as $item) {
    $uuid = uuid_gen();
    
    if(copy_file("{$doc_root}/data/images/docs/".$paciente[$item], $uuid, "{$doc_root}/dados/documents")) {
      $ext = pathinfo($src, PATHINFO_EXTENSION);
      $fname =  $uuid . '.' . $ext;
      
      $stmtx = $pdo->prepare("UPDATE `PACIENTES` SET `{$item}` = :{$item} WHERE token = :token");
      $stmtx->bindValue(":{$item}", $fname);
	  $stmtx->bindValue(":token", $paciente['token']);
      
      try {
        $stmtx->execute();
      } catch(PDOException $e) {
        echo $e->getMessage();
      }
    } else {
      echo "Erro ao Copiar";
    }
  }
}