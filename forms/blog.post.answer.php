<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmtx = $pdo->prepare("INSERT INTO `BLOG_POSTS`(`medico_token`, `post_ref`, `answer_ref`, `content`, `post_type`, `anexos`) VALUES (:medico_token, :post_ref, :answer_ref, :content, :post_type, :anexos)");
$stmtx->bindValue(':medico_token', $_POST['medico_token']);
$stmtx->bindValue(':post_ref', $_POST['post_id']);
$stmtx->bindValue(':content', base64_encode($_POST['text']));
$stmtx->bindValue(':post_type', 'ANSWER');
$stmtx->bindValue(':answer_ref', $_POST['answer_ref']);
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
        'text' => $ex->getMessage()
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, 15|64|128);