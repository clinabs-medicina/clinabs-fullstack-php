<?php
$page = new stdClass();
$page->title = 'Faturamento';
$page->content = 'financeiro/main.php';
$page->bc = true;
$page->name = 'link_financeiro';
$page->require_login = true;
$page->includePlugins = true;
require_once('../config.inc.php');


$cobrancas = [];

$stmt = $pdo->query("SELECT id, asaas_payload FROM VENDAS WHERE asaas_payload != '[]' ORDER BY id DESC");

foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $cobranca = json_decode($item['asaas_payload'], true);
     $cobrancas[] = $cobranca;
}

$saldo = $asaas->accountBalance();
$balance = $asaas->paymentsBalance();

$previsto = ArrayList::Sum(ArrayList::Filter($cobrancas, 'status', 'CONFIRMED'), 'value');
$pendente = ArrayList::Sum(ArrayList::Filter($cobrancas, 'status','PENDING'), 'value');

$cobrancas = json_decode(json_encode($cobrancas));


require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';