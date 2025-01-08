<?php
header('Content-Type: text/html; charset=utf-8');

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/session.php';

$page = new stdClass();
$page->title = 'Perfil';
$page->content = 'perfil/main.php';
$page->bc = true;
$page->name = 'link_perfil';
$page->require_login = true;

$useDT = true;
$useSelector = true;
$useEditor = true;
$useCalendar = true;

$conf = new stdClass();

function parse_bool($value)
{
    if (gettype($value) == 'string') {
        $value = strtolower($value);
        if ($value == 'true') {
            return true;
        } else {
            return false;
        }
    } elseif (gettype($value) == 'boolean') {
        return $value;
    } elseif (gettype($value) == 'integer') {
        if ($value == 1) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

if (isset($_GET['profile'])) {
    header('Content-Type: application/json');
    echo json_encode($user, JSON_PRETTY_PRINT);
} 

elseif (isset($_GET['token'])) {
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

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':token', $tk);
    $stmt->execute();
    $obj = $stmt->fetch(PDO::FETCH_OBJ);

    $tableName = $obj->tipo . 'S';
    $sq = "SELECT * FROM $tableName WHERE token = '$tk'";

    $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
    $stmt2->bindValue(':token', $tk);

    $stmt2->execute();
    $_user = $stmt2->fetch(PDO::FETCH_OBJ);
    $_SESSION['_user'] = $_user;

    if (isset($_GET['dump'])) {
        header('Content-Type: application/json');
        echo json_encode($_user, JSON_PRETTY_PRINT);
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/MasterPage.php';
    }
} 

else {
    $tableName = $user->tipo . 'S';
    $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
    $stmt2->bindValue(':token', $user->token);

    $stmt2->execute();
    $_user = $stmt2->fetch(PDO::FETCH_OBJ);
    $_SESSION['user'] = $_user;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/MasterPage.php';
}