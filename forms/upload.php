<?php

if(isset($_FILES['doc'])) {
  $file = $_FILES['doc'];
  $fileName = $file['tmp_name'];

      $supported_files = ['png', 'jpg','jpeg','pdf'];
      $images = ['png', 'jpg','jpeg'];
      $docs = ['pdf'];
      $mime = $file['type'];
      $ext = end(explode('.', $file['name']));
      
      $blob = base64_encode(file_get_contents($fileName));
      
      $path = '../data/images/docs/'.md5(uniqid().'-'.uniqid()).'.'.$ext;
      
      move_uploaded_file($fileName, $path);
      
    if(in_array($ext, $supported_files)) {
        if(in_array($ext, $images)) {
          $result = array(
             "status" => "success",
             "name" => $file['name'],
             "type" => $mime,
             "size" => filesize($fileName),
             "token" =>  uniqid(),
             "isImage" => true,
             "base64" => "data:{$mime};base,{$blob}",
             "blob" => $blob,
             "path" => basename($path)
              );
        }
        
        else {
          $result = array(
             "status" => "success",
             "name" => $file['name'],
             "type" => $mime,
             "size" => filesize($fileName),
             "token" =>  uniqid(),
             "isImage" => true,
             "base64" => "data:{$mime};base,{$blob}",
             "blob" => $blob,
             "path" => basename($path)
              );
        }
      } else {
        $result = [
          'status' => 'error',
          'text' => 'Arquivo não Suportado.'
          ];
        }
      } else {
          $result = [
          'status' => 'error',
          'text' => 'Falha ao Enviar Arquivo..'
          ];
        }

header('Content-Type: application/json');
echo json_encode($result);