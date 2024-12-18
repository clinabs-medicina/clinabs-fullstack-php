<?php
require_once 'config.inc.php';

$stmt = $pdo->prepare('SELECT * FROM `CRONTAB` WHERE `data` LIKE "%' . date('Y-m-d H:i') . '%"');

$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_OBJ);

echo '<pre>';

echo date('Y-m-d H:i:s') . '<hr>';
echo '<hr>';


foreach ($result as $item) {
    $res = $wa->sendLinkMessage(
        $item->celular,
        $item->message,
        'https://clinabs.com/agenda',
        'CLINABS',
        'Calendário de Agendamentos',
        'https://clinabs.com/assets/images/logo.png'
    );

    $stmt = $pdo->prepare('UPDATE `CRONTAB` SET `status` = :sts,`output` = :bff  WHERE `id` = :id');
    $stmt->bindValue(':sts', 'DONE');
    $stmt->bindValue(':bff', $res);
    $stmt->bindValue(':id', $item->id);

    $stmt->execute();

    $item->output = $res;

    print_r($item);
}
echo '</pre>';
