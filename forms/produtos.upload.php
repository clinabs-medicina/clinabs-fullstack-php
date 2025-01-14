<?php
if(isset($_FILES['product-image']) || isset($_FILES['product-catalog'])) {
  $target_dir = "../tmp/";


  if(isset($_FILES['product-image'])) {
    $extension = pathinfo($_FILES['product-image']['name'], PATHINFO_EXTENSION);
    $target_file = $_SERVER['DOCUMENT_ROOT'].'/data/images/produtos/'. uniqid() . "." . $extension;
    
    if (move_uploaded_file($_FILES["product-image"]["tmp_name"], $target_file)) {
      $result = [
        'icon' => 'success',
        'text' => 'Arquivo Enviado',
        'path' => basename($target_file),
        'size' => $_FILES["product-image"]["size"],
        'type' => $_FILES["product-image"]["type"],
      ];
    } else {
        $result = [
          'status' => 'error',
          'message' => 'Ocorreu um Erro ao Enviar o Arquivo.',
        ];
    }
  }else {
    $extension = pathinfo($_FILES['product-catalog']['name'], PATHINFO_EXTENSION);
    $target_file = '../data/catalogs/products/' . uniqid() . "." . $extension;
    
    if (move_uploaded_file($_FILES["product-catalog"]["tmp_name"], $target_file)) {
      $result = [
          'icon' => 'success',
          'text' => 'File uploaded successfully',
          'path' => basename($target_file),
          'size' => $_FILES["product-image"]["size"],
          'type' => $_FILES["product-image"]["type"],
        ];
      } else {
          $result = [
            'icon' => 'error',
            'text' => 'Ocorreu um Erro ao Enviar o Arquivo.',
          ];
    }
  }
}else {
  $result = [
            'icon' => 'error',
            'text' => 'Ocorreu um Erro ao Enviar o Arquivo.',
          ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);