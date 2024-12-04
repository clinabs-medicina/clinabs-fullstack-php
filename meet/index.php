<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmt = $pdo->prepare("SELECT meet FROM AGENDA_MED WHERE token = :token");
$stmt->bindValue(':token', $_REQUEST['agenda_token']);
$stmt->execute();
$list = $stmt->fetch(PDO::FETCH_OBJ);

$meet = json_decode($list->meet);

header("Location: {$meet->hostRoomUrl}");