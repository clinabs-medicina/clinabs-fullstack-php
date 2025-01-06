<?php
header('Content-Type: text/html; charset=utf-8');

if(!isset($_COOKIE['sessid_clinabs'])) {
   header('Location: /login');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}
// if(isset($_SESSION['userObj'])) {
//   $user = (object) $_SESSION['userObj'];
// }

$page = new stdClass();
$page->title = 'Meu Perfil';
$page->content = 'perfil/main.php';
$page->bc = true;
$page->name = 'link_perfil';
$page->require_login = true;

$useDT = true;
$useSelector = true;
$useEditor = true;
$useCalendar = true;

$conf = new stdClass();

function parse_bool($value) {
   if(gettype($value) == 'string') {
      $value = strtolower($value);
      if($value == 'true') {
         return true;
      }
      else {
         return false;
      }
   } else if (gettype($value) == 'boolean') {
      return $value;
   } else if (gettype($value) == 'integer') {
      if($value == 1) {
         return true;
      }
      else {
         return false;
      }
   } else {
      return false;
   }
}

   if(isset($_GET['profile'])) {
         header('Content-Type: application/json');
         echo json_encode($user, JSON_PRETTY_PRINT);
   }
   else if(isset($_GET['token'])) {
      $sql = "
     SELECT
      objeto AS tipo
   FROM
      USUARIOS AS U 
      WHERE 
      U.token = :token
      UNION ALL
    SELECT
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
      $stmt->bindValue(':token', isset($_GET['token']) ? $_GET['token'] : $user->token);
      $stmt->execute();
      $obj = $stmt->fetch(PDO::FETCH_OBJ);

      $tableName = $obj->tipo.'S';
      $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
      $stmt2->bindValue(':token', isset($_GET['token']) ? $_GET['token'] : $user->token);

      $stmt2->execute();
      $_user = $stmt2->fetch(PDO::FETCH_OBJ);

      if(isset($_GET['dump'])) {
         header('Content-Type: application/json');
         echo json_encode($_user, JSON_PRETTY_PRINT);
      } else {
         require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';
      }
      
   }
   else{
      $tableName = $user->tipo.'S';
      $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
      $stmt2->bindValue(':token', $user->token);

      $stmt2->execute();
      $_user = $stmt2->fetch(PDO::FETCH_OBJ);
      
      require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';
   }