<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmt = $pdo->query('SELECT * FROM VENDAS WHERE dueTime IS NOT NULL');
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

$res = [];

foreach($rows as $row) {
    $res[] = $asaas->deleteCobranca($row->payment_id);
}