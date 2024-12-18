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

$sql_stmt = $pdo->prepare('INSERT INTO `BLOG_POSTS`(`medico_token`, `post_ref`, `content`, `post_type`, `anexos`) VALUES (:medico_token, :post_ref, :content, :post_type, :anexos)');

$sql_stmt->bindValue(':post_ref', $data['bid']);
$sql_stmt->bindValue(':content', $data['content']);
$sql_stmt->bindValue(':anexos', json_encode($data['attachments']));
$sql_stmt->bindValue(':post_type', 'POST');
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