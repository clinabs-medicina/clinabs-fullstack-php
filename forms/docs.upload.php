<?php
$data = $_FILES['doc'];
$filename = $data['tmp_name'];
$allowed = ['pdf','jpg','png','jpeg'];
$ext = pathinfo($data['name'], PATHINFO_EXTENSION);
$file = basename($data['name']);
$newName = md5(uniqid().'-'.uniqid());
$fileSize = $data['size'];
$mime = $data['type'];
$blob = base64_encode(file_get_contents($filename));


if(in_array($ext, $allowed) && $data['error'] == 0) {
  if(move_uploaded_file($data['tmp_name'], "../data/images/docs/{$newName}.{$ext}")) {
    $resp = [
      'success' => true,
      'name' => "{$newName}.{$ext}",
      'size' => $fileSize,
      'extension' => $ext,
      'mime' => $mime,
      'base64' => "data:{$mime};base64,{$blob}",
      'blob' => $blob,
      'path' => "{$newName}.{$ext}"
      ];
  } else {
    $resp = [
      'success' => false,
      'text' => 'Falha ao Enviar o Anexo'
      ];
  }
} else {
    $resp = [
      'success' => false,
      'text' => "Arquivo Não Suportado\n\n A Extensão $ext Não é Permitido\n\n Permitidas: ".implode(', ', $allowed)
      ];
}

header('content-Ttype: application/json');
echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);