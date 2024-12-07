<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$data = $_POST;

if(isset($_FILES['anexo'])) {
    for($i = 0; $i < count($_FILES['anexo']['tmp_name']); $i++) {
      	$name = $_FILES['anexo']['name'][$i];
      	$file = $_FILES['anexo']['tmp_name'][$i];
      	$ext = pathinfo($name, PATHINFO_EXTENSION);

      	$uuid = uniqid();
      
        move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT']."/data/attachments/{$uuid}.{$ext}");
      
      $data['attachments'][] = ['name' => $name, 'uri' => "{$uuid}.{$ext}"];
    }
}

if(isset($_FILES['image'])) {
     $name = $_FILES['image']['name'];
     $file = $_FILES['image']['tmp_name'];
  	 $ext = pathinfo($name, PATHINFO_EXTENSION);
  
  	 $uuid = uniqid();
  
     move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT']."/data/attachments/{$uuid}.{$ext}");
  	$data['image'] = "{$uuid}.{$ext}";
}


$sql_stmt = $pdo->prepare('INSERT INTO `BLOG_MEDICO` (`image`, `subject`, `text_desc`, `token`, `medico_token`) VALUES (:image, :subject, :text_desc, :token, :medico_token)');

$sql_stmt->bindValue(':image', $data['image']);
$sql_stmt->bindValue(':subject', $data['desc']);
$sql_stmt->bindValue(':text_desc', base64_decode($data['content']));
$sql_stmt->bindValue(':token', uniqid());
$sql_stmt->bindValue(':medico_token', $data['medico_token']);


try {
  $sql_stmt->execute();
  $result = [
      'status' => 'success',
      'text' => 'Post Salvo'
    ];
} catch(Exception $error) {
  $result = [
      'status' => 'error',
      'text' => $error->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);