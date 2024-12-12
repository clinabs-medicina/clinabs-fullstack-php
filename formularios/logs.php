<?php
require_once('../config.inc.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['token']))
  {
    $stmtx = $pdo->prepare('INSERT INTO `clinabs_db`.`ACCESS_LOGS` (`page`, `user`,`ip`) VALUES (:page, :token, :ip);');
    $stmtx->bindValue(':page', $_SERVER['REQUEST_URI']);
    $stmtx->bindValue(':token', $user->token);
    $stmtx->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
    
    $stmtx->execute();
  }