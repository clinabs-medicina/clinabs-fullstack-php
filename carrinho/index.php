<?php
error_reporting(1);
ini_set('display_errors', 1);


$page = new stdClass();
$page->title = 'Carrinho de Compras';
$page->content = isset($_REQUEST['payment']) ? 'carrinho/payment.php':'carrinho/main.php';
$page->bc = true;
$page->name = 'link_cart';
$page->require_login = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['userObj'])) {
   $user = (object) $_SESSION['userObj'];
   $sql = "SELECT
      objeto AS tipo
   FROM
      MEDICOS AS M 
      WHERE 
      M.token = :token
      UNION ALL
      (
      SELECT
         objeto AS tipo
      FROM
         PACIENTES AS P
         WHERE 
         P.token = :token
      ) UNION ALL
      (
      SELECT
         objeto AS tipo
      FROM
      FUNCIONARIOS AS F
      WHERE 
      F.token = :token
      )";


      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':token', isset($_SESSION['token']) ? $_SESSION['token'] : $user->token);
      $stmt->execute();
      $obj = $stmt->fetch(PDO::FETCH_OBJ);

      $tableName = $obj->tipo.'S';
      
      if($tableName !== 'S') {
         $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
         $stmt2->bindValue(':token', isset($_SESSION['token']) ? $_SESSION['token'] : $user->token);

      $stmt2->execute();
      $User = $stmt2->fetch(PDO::FETCH_OBJ);

      
      }else{
         $User = false;
      }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';