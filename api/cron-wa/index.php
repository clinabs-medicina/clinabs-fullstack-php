<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$stmt = $pdo->query("SELECT * FROM CRONTAB WHERE `status` = 'PENDENTE'");

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($jobs as $job) {
    if(strtotime($job['data']) <= strtotime(date('Y-m-d H:i:s'))) {
        $wa->sendLinkMessage(
            $job['celular'], 
            $job['message'],
            $job['link'], 
            'CLINABS', 
            $job['nome'], 
            'https://clinabs.com/assets/images/logo.png'
        );

    }
}