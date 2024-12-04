<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmt = $pdo->query('SELECT * FROM IPS_BLOQUEADOS');

$ips = [];
file_put_contents("/home/blocklist.sh", "");

foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $ip) {
    $ips[] = $ip->ip;

    file_put_contents("/home/blocklist.sh","sudo fail2ban-client set recidive banip {$ip->ip}\n", FILE_APPEND);
}

header('Content-Type: application/json');
echo json_encode($ips);