<?php
require_once "../config.inc.php";

$stmt = $pdo->query("SELECT * FROM ENDERECOS WHERE token = '{$_GET['token']}'");
$street = $stmt->fetch(PDO::FETCH_OBJ);

header("content-type: application/json");
echo json_encode($json, JSON_PRETTY_PRINT);