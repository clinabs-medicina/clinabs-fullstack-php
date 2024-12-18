<?php
require_once '../config.inc.php';
$token = $_GET['token'];
$dados = [];
$db = $pdo->prepare('SELECT *,(SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome FROM `PRESCRICOES` WHERE agenda_token = :agenda_token');
$db->bindValue(':agenda_token', $token);
$db->execute();
$rows = $db->fetchAll(PDO::FETCH_ASSOC);

$i = 1;

foreach($rows as $row) {
    $item = [];
    $item['index'] = $i;
    $item['desc'] = base64_decode($row['prescricao']);
    $item['remedio'] = strtoupper(trim($row['produto_nome']));
    $item['frascos'] = $row['frascos'];
    $item['act'] = '';

    $dados['data'][] = $item;

    $i++;
}

header('Content-Type: application/json');
echo json_encode($dados, JSON_PRETTY_PRINT);