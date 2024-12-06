<?php
header('Content-Type: text/html; charset=utf-8');

if(!isset($_COOKIE['sessid_clinabs'])) {
   header('Location: /login');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';

$conf = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/.auth.json'));

$page = new stdClass();
$page->title = 'Meu Perfil';
$page->content = 'perfil/main.php';
$page->bc = true;
$page->name = 'link_perfil';
$page->require_login = true;


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

try {
   $date_today = date('Y-m-d');
   $stmtc = $pdo->query("SELECT COUNT(*) as cliques,ip,timestamp FROM `ACCESS_LOGS` WHERE timestamp LIKE '{$date_today}%' GROUP BY ip ORDER BY timestamp ASC");
   $visitas_hoje = $stmtc->rowCount();
} catch(Exception $ex) {
   $visitas_hoje = 0;
}

function getAccounts($file) {
   $accounts = [];

   $handle = fopen($file, "r");
   if ($handle) {
       while (($line = fgets($handle)) !== false) {
          $item = explode('|', $line);

          $accounts[strtolower($item[0])] = $item[1];
       }

       fclose($handle);
   }

   return $accounts;
}




$mail_accounts = getAccounts($file);

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
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

      require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';
   }
   else{
      $tableName = $user->tipo.'S';
      $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
      $stmt2->bindValue(':token', $user->token);

      $stmt2->execute();
      $_user = $stmt2->fetch(PDO::FETCH_OBJ);
      
      require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';
   }


 
