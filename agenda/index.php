<?php
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
$page = new stdClass();
$page->title = 'Agenda do Médico';
$page->content = isset($_GET['page']) ? 'agenda/'.$_GET['page'].'.php':'agenda/main.php';
$page->bc = true;
$page->name = 'link_agenda';
$useDT = true;
$useSelector = true;
$useEditor = true;

if(!isset($_SESSION['token']) && !isset($_GET['page'])) {
    $page->require_login = true;
}

$page->includePlugins = true;

if($user->tipo == 'FUNCIONARIO' && isset($_SESSION['token'])) {
    $stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE MD5(token) = :token");
    $stmt2->bindValue(':token', $_SESSION['token']);

    $stmt2->execute();
    $_user = $stmt2->fetch(PDO::FETCH_OBJ);
    
}else {
    
    $stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE MD5(token) = :token");
    

   try {
       $stmt2->bindValue(':token', $_SESSION['token']);
        $stmt2->execute();
        $_user = $stmt2->fetch(PDO::FETCH_OBJ);
   }catch(PDOException $ex) {
       
   }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';