<?php
header('Content-Type: text/html; charset=utf-8');

if(!isset($_COOKIE['sessid_clinabs'])) {
   header('Location: /login');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';

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
      
      $tk = $_GET['token'];
      error_log("sql: perfil index \$tk: $tk \r\n" . PHP_EOL);
      error_log("sql: perfil index \$sql: $sql \r\n" . PHP_EOL);
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':token', $tk);
      $stmt->execute();
      $obj = $stmt->fetch(PDO::FETCH_OBJ);

      $tableName = $obj->tipo.'S';
      error_log("perfil index \$tableName: $tableName \r\n" . PHP_EOL);
      $sq = "SELECT * FROM $tableName WHERE token = '$tk'";
      error_log("perfil index \$tableName: $sq \r\n" . PHP_EOL);

      $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
      $stmt2->bindValue(':token', $tk);

      $stmt2->execute();
      $_user = $stmt2->fetch(PDO::FETCH_OBJ);
      $_user->tipo = $obj->tipo;      
      $_user->objeto = $obj->tipo;      
      error_log("perfil index \$_user->nome_completo: $_user->nome_completo \r\n" . PHP_EOL);
      if(isset($_GET['dump'])) {
         error_log("perfil index json encode \r\n" . PHP_EOL);

         header('Content-Type: application/json');
         echo json_encode($_user, JSON_PRETTY_PRINT);
      } else {
         $_SESSION['userObjEditPerfil'] = $_user;
         error_log("perfil index MasterPage.php seted user \r\n" . PHP_EOL);
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