<?php
ini_set('display_errors', 1);
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['user'])) {
    try {
       $user = (object) $_SESSION['user'];
   } catch (PDOException $e) {
 
   }
}
$pg = $_GET['page'];
try{    
    error_log("Valor da variável agenda index \$_GET['page']: $pg \r\n" . PHP_EOL, 3, 'C:\xampp\htdocs\errors.log');
} catch (PDOException $e) {
}

$page = new stdClass();
$page->title = 'Agenda do Médico';
$page->content = isset($_GET['page']) ? 'agenda/'.$_GET['page'].'.php':'agenda/main.php';
$page->bc = true;
$page->name = 'link_agenda';
try{    
    error_log("Valor da variável agenda index \$page->content: $page->content \r\n" . PHP_EOL, 3, 'C:\xampp\htdocs\errors.log');
} catch (PDOException $e) {
}
 
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
       try{    
        error_log("Valor da variável agenda index \$_user: $_user \r\n" . PHP_EOL, 3, 'C:\xampp\htdocs\errors.log');
        } catch (PDOException $e) {
        }
    
   }catch(PDOException $ex) {
       
   }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';