<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmtx = $pdo->prepare("INSERT INTO `BLOG_POSTS`(`medico_token`, `post_ref`, `content`, `post_type`, `anexos`) VALUES (:medico_token, :post_ref, :content, :post_type, :anexos)");
$stmtx->bindValue(':medico_token', $_POST['medico_token']);
$stmtx->bindValue(':post_ref', $_POST['post_id']);
$stmtx->bindValue(':content', base64_encode($_POST['text']));
$stmtx->bindValue(':post_type', 'COMMENT');
$stmtx->bindValue(':anexos', '[]');

try{
    $stmtx->execute();

    $result = [
        'status' => 'success',
        'text' => 'Comentário Postado com Sucesso!'
    ];
} catch(Exception $ex) {
    $result = [
        'status' => 'error',
        'text' => 'Erro ao postar Comentário'
    ];
}

header('Content-Type: application/json');
echo json_encode($result, 15|64|128);