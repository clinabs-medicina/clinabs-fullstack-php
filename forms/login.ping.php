<?php
$no_debug = true;
/*
require_once("{$_SERVER['DOCUMENT_ROOT']}/config.inc.php");
date_default_timezone_set($_REQUEST['tz']);

if(isset($_COOKIE['sessid_clinabs'])) {
  $session_id = $_COOKIE['sessid_clinabs'];

  $stmt0 = $pdo->prepare('UPDATE `SESSIONS` SET `ip` = :ip,`last_ping` = :lp WHERE session_id = :session_id');
  $stmt0->bindValue(':session_id', $session_id);
  $stmt0->bindValue(':lp', date('Y-m-d H:i:s', strtotime(str_replace('/','-', $_REQUEST['timestamp']))));
  $stmt0->bindValue(':ip', $_GET['ip']);
  $ds =  $_REQUEST;

  try {
    $stmt0->execute();

    $data = ['status' => 'success', 'data' => $ds, 'tz' => $_COOKIE['timeZone'], 'affected' => $stmt0->rowCount()];
  
    if($stmt0->rowCount() == 0) {
      
    }
  } catch(PDOException $ex) {
    $data = ['status' => 'error','reason' => $ex->getMessage(), 'data' => $ds, 'affected' => $stmt0->rowCount()];
  }

  header('Content-Type: application/json');
  echo json_encode($data, JSON_PRETTY_PRINT);
}

*/