<?php
require_once('../config.inc.php');
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['receita'])) {
  $blob = base64_decode(str_replace('data:application/pdf;base64,', '', $data['receita']));
  $filename = uniqid().'.pdf';
  
  file_put_contents($_SERVER['DOCUMENT_ROOT']."/data/receitas/assinadas/{$filename}", $blob);
    
  if(file_exists($_SERVER['DOCUMENT_ROOT']."/data/receitas/assinadas/{$filename}")) {
    $stmt_receita = $pdo->prepare('UPDATE AGENDA_MED SET file_signed = :file_signed WHERE token = :token');
    $stmt_receita->bindValue(':file_signed', "/data/receitas/assinadas/{$filename}");
    $stmt_receita->bindValue(':token', $data['token']);
    
    try {
      	$stmt_receita->execute();
      
      $res = [
        'status' => 'success',
        'text' => 'Upload Realizado com Sucesso!'
      ];
    } catch(Exception $error) {
      $res = [
        'status' => 'error',
        'text' => 'Falha no Upload!'
      ];
    }
   
  }
}else {
  $res = [
        'status' => 'warning',
        'text' => 'Nenhum Arquivo Enviado.'
      ];
      
}


$res = [
        'status' => 'warning',
        'text' => 'Nenhum Arquivo Enviado.'
      ];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);